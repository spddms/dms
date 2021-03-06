<?php

namespace app\controllers\dms;

use Yii;
use \app\facades\common\CommonFacade;
use \app\facades\dms\LibraryFacade;


class LibraryController extends \yii\web\Controller {

    public $enableCsrfValidation = false;

   
    
    public function behaviors(){
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => [ 
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
                            
                        ],
                'rules' => [
                    [
                        'actions' => [
                            'list', 'add', 'edit', 'delete', 'view', 'activatedeactivate', 'viewlist'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            
        ];
    }
    
    
    public function beforeAction($e){
        
        $status = CommonFacade::authorize(Yii::$app->request);
        if(!$status){
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/adminuser/home/index"));
        } else {
            return parent::beforeAction($e);
        }
    }  
    
    /*
     * function for getting list of roles
     * @author: Waseem
     */
    public function actionList() {
        $lang = CommonFacade::getLanguage();
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        $id = Yii::$app->user->getId();
        if($id){
            $list = \app\models\Lookups::find()
                    ->where(['type' => 1, 'is_delete'=>1])
                    ->orderBy('id DESC')->all();
        }    
        return $this->render('list', array('model'=>$list, 'permission'=>$permission, 'lang'=>$lang));
    }
    
    /*
    public function actionViewlist(){
        
        if(Yii::$app->request->get()){
            $request = Yii::$app->request->get();
            $facade = new VendorFacade();
            $response = $facade->viewList($request);
            return json_encode($response);
        } else {
            
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    } 
     * 
     */
    
    
    
    /*
     * function for viewing assessment requests complete data
     * @author: Waseem
     */
    public function actionView(){
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
            $id =  $_REQUEST['Id'];
            if($id){
                $facade = new VendorFacade();
                $response = $facade->editVendor($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('view', array('model'=>$model, 'reasonList'=>$reasonList, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
    }
    
     public function actionAdd() {
        $model = new Vendor();
        if(Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            $facade = new VendorFacade();
            $response = $facade->addVendor($request);
            $code = $response['CODE'];
            $MSG = $response['MESSAGE'];
            
            if ($code == 200){
                Yii::$app->getSession()->setFlash('success', $MSG);
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            } else if($code == 100){
                $model = $response['DATA'];
                Yii::$app->getSession()->setFlash('error', $MSG);
            }
            
        }
     	return $this->render('add', array('model'=>$model));
    }
    
     /*
     * function for opening a vendor's data in edit mode
     * @author: Waseem
     */
    public function actionEdit() {
        $permission = CommonFacade::getPermissions(Yii::$app->request);
        if(isset($_REQUEST['Id'])){
        $id =  $_REQUEST['Id'];
            if($id){
                $facade = new VendorFacade();
                $response = $facade->editVendor($id);

                $code = $response['CODE'];
                $MSG = $response['MESSAGE'];

                if ($code == 200){
                    $model = $response['DATA'];
                    return $this->render('add', array('model'=>$model, 'permission'=>$permission));
                } else if($code == 100){
                    $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
                }
            } else {
                $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
            }
        } else {
            $this->redirect(Yii::$app->urlManager->createUrl("index.php/dms/vendor/list"));
        }
        
    }
    
    
      /*
     * function for deleteing permission
     * @author: Waseem
     */
    public function actionDelete(){
        $request = Yii::$app->request;
        $id = $request->post('id');
        if($id){
            $facade = new VendorFacade();
            $response = $facade->deleteVendor($id);
            return json_encode($response);
        }
    }
    
}
