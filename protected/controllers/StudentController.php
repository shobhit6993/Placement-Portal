<?php

class StudentController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','viewjobs','viewoffers','index','view'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Student;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Student']))
		{
			$model->attributes=$_POST['Student'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->st_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Student']))
		{
			$model->attributes=$_POST['Student'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->st_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
        $model=Login::model()->find("id=?",array($id));
        $model->delete();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */

    // OLD ACTION INDEX
	/*public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Student');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}*/

    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('Student',array(
            'criteria'=>array(
                'condition'=>'st_id='.Yii::app()->user->id,

            )));
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Student('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Student']))
			$model->attributes=$_GET['Student'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    public function actionViewjobs(){
        //PPO accept Check
        $ppoCheck=  Yii::app()->db->createCommand("select * from offers where st_id = ".Yii::app()->user->id." and ppo = 'Y' and accepted = 'Y'")->queryAll();
        if(count($ppoCheck)!=0)
        {
            
            Yii::app()->user->setFlash('notice','You have already accepted your PPO and hence, will be able to apply to ONLY ONE company');
        }

        $stud = Student::model()->findByPk(Yii::app()->user->id);
        $st = new Student();
        $st->st_id = Yii::app()->user->id;

        $dept = $stud->getAttribute("dept");
        $programme = $stud->getAttribute("programme");

        $sql =  Yii::app()->db->createCommand("select count(*) as cnt from student where dept=\"".$dept."\" and programme = \"".$programme."\"")->queryRow();
        $totalReg = $sql['cnt'];

        $sql =  Yii::app()->db->createCommand("select count(*) as cnt from (select distinct(oTemp.st_id) from offers as oTemp) as o, student as s where o.st_id = s.st_id and s.dept=\"".$dept."\" and s.programme = \"".$programme."\"")->queryRow();
        $actualStudPlaced = $sql['cnt'];

        if($totalReg==0) $percent=0;
        else $percent = ($actualStudPlaced*100)/$totalReg;

        if($percent>=80)
        {
        	Yii::app()->user->setFlash('notice','Your dept is on a roll. More than 80% placed! You may now apply for a new job profile even if you accepted an offer before.');
        }



        $sqlcount =  Yii::app()->db->createCommand("select count(*) from job_profile_branches where dept = \"".$dept."\"")->queryScalar();
        $sql = "select * from job_profile as j, company as c where j.approved='Y' and (c.c_id,j.j_id) in (select c_id, j_id from job_profile_branches where dept = \"".$dept."\")";

        $dataProvider = new CSqlDataProvider($sql, array(
            'db' => Yii::app()->db,
            'keyField' => 'c_id',
            'totalItemCount' => $sqlcount
        ));

        $this->render('viewJobProfiles',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionViewoffers(){

        $id = Yii::app()->user->id;

        $sqlcount =  Yii::app()->db->createCommand("select count(*) from job_profile as j, offers as o where j.j_id=o.j_id and j.c_id=o.c_id and o.st_id=".$id)->queryScalar();
        $sql = "select * from job_profile as j, offers as o,company as c where j.j_id=o.j_id and j.c_id=o.c_id and c.c_id=j.c_id and o.st_id=".$id;
        $dataProvider = new CSqlDataProvider($sql, array(
            'db' => Yii::app()->db,
            'keyField' => 'j_id',
            'totalItemCount' => $sqlcount
        ));

        $this->render('viewOffers',array(
            'dataProvider'=>$dataProvider,
        ));
    }
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Student the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Student::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Student $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='student-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
