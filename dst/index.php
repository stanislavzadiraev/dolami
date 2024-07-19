<?php

include 'client.php';

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function test($key){
    if ( isset($_GET) && isset($_GET[$key]) )
        return strval($_GET[$key]);
    else if ( isset($_POST) && isset($_POST[$key]) )
        return strval($_POST[$key]);
    else
        return '';
}

function offile($name) {
    $handle = fopen($name, "r");
    $contents = fread($handle, filesize($name));
    fclose($handle);

    return $contents;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function form() {echo(' 
    <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">
      <h3>Заявка</h3>

      <h3>Заявление</h3>
        <label for="id">id:</label>
        <input type="text" name="id" value="'.test("id").'"><br>

        <label for="amount">amount:</label>
        <input type="text" name="amount" value="'.test("amount").'"><br>

        <label for="prepaid">prepaid:</label>
        <input type="text" name="prepaid" value="'.test("prepaid").'"><br>

        <label for="items">items:</label>
        <input type="text" name="items" value="'.test("items").'"><br>

      <h3>Заявитель</h3>
        <label for="first_name">first_name:</label>
        <input type="text" name="first_name" value="'.test("first_name").'"><br>

        <label for="last_name">last_name:</label>
        <input type="text" name="last_name" value="'.test("last_name").'"><br>

        <label for="phone">phone:</label>
        <input type="text" name="phone" value="'.test("phone").'"><br>

        <label for="email">email:</label>
        <input type="text" name="email" value="'.test("email").'"><br>        

      <input type="submit" value="Отправить">
    </form> 
    ');};

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (
        $_SERVER['REQUEST_METHOD'] === 'POST' && 
        strlen(test("id")) && strlen(test("amount")) && strlen(test("prepaid")) && strlen(test("items")) &&
        strlen(test("first_name")) && strlen(test("last_name")) && strlen(test("phone")) && strlen(test("email"))
    ) 
    {
        $client = new Client(
            offile(realpath('./../' . 'sequre/user')),
            offile(realpath('./../' . 'sequre/word')
        ));
        $client->setCertPath(realpath('./../' . 'sequre/certificate.pem'));
        $client->setKeyPath(realpath('./../' . 'sequre/private.key'));

        echo "client:<br>"; print_r($client); echo "<br>";

        $data = [
            'order' => [
                'id' => $_POST['id'],
                'amount' => $_POST['amount'],
                'prepaid_amount' => $_POST['prepaid'],
                'items' => $_POST['items'],
            ],
            'client_info' => [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
            ],
            'notification_url' => 'note',
            'fail_url' => 'fail',
            'success_url' => 'done',
        ];

        try{
            $response = $client->create($data);

            echo "responce:<br>"; print_r($response); echo "<br>";
        }
        catch (Exception $e) {
            echo "exception:<br>"; print_r($e); echo "<br>";
        }
    }
else
    form();
?>