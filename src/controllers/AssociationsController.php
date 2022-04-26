<?php

/**
 * 
 */

class AssociationsController extends MainController
{
    public $associations;
    public $association;
    public $unpublishedNews;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_ASSOCIATIONS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->associations = new AssociationsList();
        $this->unpublishedNews = new NewsList();
    }

    public function indexMain()
    {
        if (
            !UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                UserSession::getUser()->getPermissions(),
                $this->premissionsRequired,
                false
            )
        )
            return;

        $this->loadModel('associations/AssociationsModel');

        if (isset($_POST['enterAssociation']) && $data = checkArray($_POST['enterAssociation'], 'id')) {
            $this->model->enterAssocition($data);
        }

        if (isset($_POST['payQuota']) && $data = checkArray($_POST['payQuota'], 'user', 'association', 'quantity'))
            $this->model->userPayQuota($data['user'], $data['association'], $data['quantity']);

        if (isset($_POST['create']))
            $this->model->createAssociation();

        $this->model->search();

        require VIEWS_PATH . '/associations/index.php';
    }

    public function page()
    {
        echo "page";
    }

    public function admni()
    {
        if (!UserSession::getUser()->isLoggedIn())
            UsersManager::getTools()->getRedirect()->redirect();

        if (!isset($this->parameters[0]))
            UsersManager::getTools()->getRedirect()->redirect();

        $this->loadModel('associations/AssociationsAdmniModel');

        $this->association = $this->model->getAssociationByNickname($this->parameters[0]);

        $payedDues = $this->model->userPayedQuotas();

        if (!isset($this->association))
            return;

        $permissions = $this->model->userAdmniPermissions(UserSession::getUser(), $this->association);

        if (!UsersManager::getTools()->getPremissionsManager()->checkPermissions(
            $permissions,
            PermissionsManager::AP_PARTNER_ADMNI,
            false
        ))
            return;

        if (isset($_POST['create']))
            $this->model->createNews($this->association);

        if (isset($_POST['edit']))
            $this->model->setNewsEdition($_POST['edit']['news']);

        if (isset($_POST['publish']))
            $this->model->publishNews($_POST['publish']['news']);

        if (isset($_POST['event']))
            $this->model->createEvent($this->association);

        if (isset($_POST['image']))
            $this->model->createImage($this->association);

        if (isset($_POST['users']['change']))
            $this->model->changePremissionsOnAssoc();

        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        require VIEWS_PATH . '/associations/admni.php';

        require VIEWS_PATH . '/includes/footer.php';
    }
}

