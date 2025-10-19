<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar instância do Database e obter conexão
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        try {
            // Tipos de cupons pré-definidos baseados no protótipo
            $tipos_cupons = [
                [
                    'codigo' => 'CHA50',
                    'desconto_percentual' => 50,
                    'descricao' => '50% de desconto em chás - Válido somente para produtos selecionados'
                ],
                [
                    'codigo' => 'DOCE25',
                    'desconto_percentual' => 25,
                    'descricao' => '25% de desconto em doces - Válido somente para produtos selecionados'
                ],
                [
                    'codigo' => 'CAFE10',
                    'desconto_percentual' => 10,
                    'descricao' => '10% de desconto em cafés - Válido somente para produtos selecionados'
                ],
                [
                    'codigo' => 'FRETE0',
                    'desconto_fixo' => 15.00,
                    'descricao' => 'Frete grátis - Válido para compras acima de R$ 50,00'
                ],
                [
                    'codigo' => 'COMBO20',
                    'desconto_percentual' => 20,
                    'descricao' => '20% de desconto em combos - Válido somente para produtos selecionados'
                ]
            ];
            
            // Sortear um cupom aleatório
            $cupom_sorteado = $tipos_cupons[array_rand($tipos_cupons)];
            
            // Verificar se o cupom já existe
            $query_verificar = "SELECT id_cupom FROM cupons WHERE codigo = :codigo";
            $stmt_verificar = $conn->prepare($query_verificar);
            $stmt_verificar->bindParam(':codigo', $cupom_sorteado['codigo']);
            $stmt_verificar->execute();
            
            if ($stmt_verificar->rowCount() == 0) {
                // Data de validade (15 dias a partir de hoje)
                $validade = date('Y-m-d', strtotime('+15 days'));
                
                // Inserir no banco
                $query = "INSERT INTO cupons (codigo, descricao, desconto_percentual, desconto_fixo, ativo, validade) 
                          VALUES (:codigo, :descricao, :desconto_percentual, :desconto_fixo, 1, :validade)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':codigo', $cupom_sorteado['codigo']);
                $stmt->bindParam(':descricao', $cupom_sorteado['descricao']);
                
                // Lidar com valores NULL corretamente
                $desconto_percentual = isset($cupom_sorteado['desconto_percentual']) ? $cupom_sorteado['desconto_percentual'] : null;
                $desconto_fixo = isset($cupom_sorteado['desconto_fixo']) ? $cupom_sorteado['desconto_fixo'] : null;
                
                $stmt->bindParam(':desconto_percentual', $desconto_percentual);
                $stmt->bindParam(':desconto_fixo', $desconto_fixo);
                $stmt->bindParam(':validade', $validade);
                
                if ($stmt->execute()) {
                    $_SESSION['mensagem_sucesso'] = [
                        'tipo' => 'success',
                        'texto' => '🎉 Parabéns! Você ganhou um cupom de ' . 
                                  (isset($cupom_sorteado['desconto_percentual']) ? 
                                   $cupom_sorteado['desconto_percentual'] . '%' : 
                                   'R$ ' . number_format($cupom_sorteado['desconto_fixo'], 2, ',', '.')) . 
                                  '! Código: ' . $cupom_sorteado['codigo']
                    ];
                }
            } else {
                $_SESSION['mensagem_sucesso'] = [
                    'tipo' => 'info',
                    'texto' => '🎁 Você já possui este cupom! Tente novamente.'
                ];
            }
            
        } catch (PDOException $e) {
            $_SESSION['mensagem_sucesso'] = [
                'tipo' => 'error',
                'texto' => 'Erro ao gerar cupom: ' . $e->getMessage()
            ];
        }
    } else {
        $_SESSION['mensagem_sucesso'] = [
            'tipo' => 'error',
            'texto' => 'Erro de conexão com o banco de dados.'
        ];
    }
    
    header('Location: cupons.php');
    exit();
}
?>