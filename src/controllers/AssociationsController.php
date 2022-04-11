<?php

/**
 * 
 */

class AssociationsController extends MainController
{
    public function indexMain()
    {
        $this->premissionsRequired = PermissionsManager::P_VIEW_ASSOCIATIONS;

        if (
            !$this->userSession->permissionManager->checkUserPermissions(
                $this->userSession->user,
                $this->premissionsRequired,
                false
            )
        )
            return;

        $this->model = $this->load_model('associations/AssociationsCreateModel.php');

        require VIEWS_PATH . '/associations/index.php';
    }
}