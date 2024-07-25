<?php

include 'client.php';

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// общее 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function takeargs($server){
    switch($server['REQUEST_METHOD']){
        case 'GET': return $_GET;
        case 'POST': return $_POST;
    };

    return [];
}

function fileread($name){
    return implode("\n", file($name));
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// формы
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function testargs($args, $form){
    foreach($form as $item => $data)
        if (!isset($args[$item]) || !$data['test']($args[$item]))
            return false;

    return true;
}

function fillform($form, $args){
    $result = '';

    $result .= '<form action="'.htmlspecialchars($_SERVER["SCRIPT_NAME"]).'" method="POST">';

    foreach($form as $item => $data){
        $value = '';
        if (isset($args[$item]))
            $value = $args[$item];
        $result .= '<label for="'.$item.'">'.$data['name'].':</label>';
        $result .= '<input name="'.$item.'" type="'.$data['type'].'" value="'.$value.'"><br>';
    };

    $result .= '<input type="submit" value="Отправить"></form>';
    
    return $result;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// долями, формы
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function test($data){
    return strlen($data);
}

$form = [
    'id' => [
        'name' => 'Номер Заказа',
        'test' => 'test',
        'type' => 'text',
    ],
    'amount' => [
        'name' => 'Сумма',
        'test' => 'test',
        'type' => 'number',
    ],
    'prepaid' => [
        'name' => 'Скидка',
        'test' => 'test',
        'type' => 'number',
    ],
    'items' => [
        'name' => 'Позиции',
        'test' => 'test',
        'type' => 'text',
    ],
    'first_name' => [
        'name' => 'Имя',
        'test' => 'test',
        'type' => 'text',
    ],
    'last_name' => [
        'name' => 'Фамилия',
        'test' => 'test',
        'type' => 'text',
    ],
    'phone' => [
        'name' => 'Телефоный номер',
        'test' => 'test',
        'type' => 'tel',
    ],
    'email' => [
        'name' => 'Почтовый ящик',
        'test' => 'test',
        'type' => 'email',
    ],
];

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// долями, клиент
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function client($args){
    try{
        $client = new Client(
            fileread(realpath('./../' . 'sequre/user')),
            fileread(realpath('./../' . 'sequre/word')
        ));
        $client->setCertPath(realpath('./../' . 'sequre/certificate.pem'));
        $client->setKeyPath(realpath('./../' . 'sequre/private.key'));

        $data = [
            'order' => [
                'id' => $args['id'],
                'amount' => $args['amount'],
                'prepaid_amount' => $args['prepaid'],
                'items' => $args['items'],
            ],
            'client_info' => [
                'first_name' => $args['first_name'],
                'last_name' => $args['last_name'],
                'phone' => $args['phone'],
                'email' => $args['email'],
            ],
            'notification_url' => 'note',
            'fail_url' => 'fail',
            'success_url' => 'done',
        ];

        return $client->create($data);

    }
    catch (Exception $e) {
        echo "client:<br>"; print_r($client); echo "<br>";
        echo "exception:<br>"; print_r($e); echo "<br>";
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// частное
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(!testargs(takeargs($_SERVER), $form)){
    print(fillform($form, takeargs($_SERVER)));
}else{
    print(client(takeargs($_SERVER)));
};


?>