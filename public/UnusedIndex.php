<?php

$app->get('/allusers', function (Request $request, Response $response) {

    $db = new DbOperations;

    $users = $db->getAllUsers();

    $response_data = array();

    $response_data['error'] = false;
    $response_data['users'] = $users;

    $response->write(json_encode($response_data));

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});


$app->put('/updateuser/{id}', function (Request $request, Response $response, array $args) {

    $id = $args['id'];

    if (!haveEmptyParameters(array('email', 'name', 'school'), $request, $response)) {

        $request_data = $request->getParsedBody();
        $email = $request_data['email'];
        $name = $request_data['name'];
        $school = $request_data['school'];


        $db = new DbOperations;

        if ($db->updateUser($email, $name, $school, $id)) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'User Updated Successfully';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user;

            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Please try again later';
            $user = $db->getUserByEmail($email);
            $response_data['user'] = $user;

            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    }

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->put('/updatepassword', function (Request $request, Response $response) {

    if (!haveEmptyParameters(array('currentpassword', 'newpassword', 'email'), $request, $response)) {

        $request_data = $request->getParsedBody();

        $currentpassword = $request_data['currentpassword'];
        $newpassword = $request_data['newpassword'];
        $email = $request_data['email'];

        $db = new DbOperations;

        $result = $db->updatePassword($currentpassword, $newpassword, $email);

        if ($result == PASSWORD_CHANGED) {
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'Password Changed';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else if ($result == PASSWORD_DO_NOT_MATCH) {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'You have given wrong password';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else if ($result == PASSWORD_NOT_CHANGED) {
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Some error occurred';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    }

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);
});

$app->delete('/deleteuser/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];

    $db = new DbOperations;

    $response_data = array();

    if ($db->deleteUser($id)) {
        $response_data['error'] = false;
        $response_data['message'] = 'User has been deleted';
    } else {
        $response_data['error'] = true;
        $response_data['message'] = 'Plase try again later';
    }

    $response->write(json_encode($response_data));

    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});
