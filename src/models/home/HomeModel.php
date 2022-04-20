<?php

/**
 * 
 */

class HomeModel extends MainModel
{
    public function getUserAssociations()
    {
        $associations = $this->db->query('SELECT * FROM `usersAssociations` WHERE `user` = ' . UserSession::getUser()->id . ';');

        if (!$associations)
            return;

        foreach ($associations->fetchAll(PDO::FETCH_ASSOC) as $association) {
            $this->controller->userAssociations->add($this->instanceAssociationByID($association['association']));
        }
    }

    private function instanceAssociationByID(int $id)
    {
        $association = $this->db->query("
            SELECT * FROM `associations`
            INNER JOIN `usersAssociations`
            ON `associations`.`id` = `usersAssociations`.`association`
            WHERE `associations`.`id` = $id
            AND `usersAssociations`.`role` = " . PermissionsManager::AP_PRESIDENT . ";");

        if (!$association)
            return;

        $association = $association->fetch(PDO::FETCH_ASSOC);

        $user = $this->instanceUserByID($association['user']);
        return $user->initAssociation(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
        );
    }

    private function instanceUserByID(int $id)
    {
        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        $user = $user->fetch(PDO::FETCH_ASSOC);

        return new User(
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            $user['telephone'],
            $user['permissions'],
            false,
            $id
        );
    }

    public function createAssociation()
    {
        if (
            !UsersManager::getPermissionsManager()->checkUserPermissions(
                $this->controller->userSession->user,
                PermissionsManager::P_CREATE_ASSOCIATIONS,
                false
            )
        )
            return;

        $association = $_POST['create'];

        unset($_POST['create']);

        if (
            $this->db->query(
                'SELECT * FROM `associations` WHERE `name` = ?;',
                [
                    $association['name'],
                ]
            )->fetchAll()
        )
            return;

        if (!$association['address'])
            unset($association['address']);

        if ($association['phone'] == 'yours' && !isset($this->controller->userSession->user->telephone))
            return;

        if ($association['phone'] == 'new' && !isset($association['int'], $association['number']))
            return;

        $association['telephone'] = $association['phone'] == 'new'
            ? '+' . $association['int'] . ' ' . $association['number']
            : $this->controller->userSession->user->telephone;

        unset($association['phone'], $association['int'], $association['number']);

        $this->db->insert('associations', array_merge(
            $association,
            [
                'president' => $this->controller->userSession->user->id
            ]
        ));
    }
}