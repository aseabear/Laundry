<?php
// 本类由系统自动生成，仅供测试用途
import("MISP.Action.BaseAction");
abstract  class EasyUITableAction extends BaseAction 
{
    abstract protected function GetModel();
    
    public function Validator($obj)
    {
        $this->LogWarn("the validator is empty ");
        return SUCCESS;
    }
    private function  GetObj()
    {
        $Req = $this->GetReqObj();
        $obj = $Req->obj;
        return $obj;
    }
    private function  GetObjArray()
    {
    	$Req = $this->GetReqObj();
    	$obj = $Req->obj;
    	return $this->objectToArray($obj);
    }
    protected function GetTableCondition()
    {
        return null;
    }
    private function GetPage()
    {
        if(null == $_POST['page'])
        {
            $page['currentPage'] = 0;
            $this->LogWarn("the crrent page is empty");
        }
        else
        {
            $page['currentPage'] = $_POST['page']-1;
        } 
        if(null == $_POST['rows'])
        {
            $page['pageSize'] = 10;
            $this->LogWarn("the page size is empty");
        }
        else
        {
            $page['pageSize'] = $_POST['rows'];
        }
        $this->LogInfo("the current page is ".$page['currentPage'].",the page size is ".$page['pageSize']);
        return $page;
    }
    public function LoadPage()
    {
        $db =  $this->GetModel();
        $this->LoadPageTable($db,$this->GetTableCondition());
    }
    
    public  function LoadAll()
    {
        
    }
    public function Show()
    {
        $db = $this->GetModel();
        $this->ShowModel($db);
    }
    public function ShowModel($model,$condition=null)
    {
    	$this->LogInfo("show");
    	$this->LogInfo($condition['user_id']);
    	$obj = $this->GetObj();
		$this->LogErr($obj);
		if($condition == null)
		{
			$result = $model->find($obj);
		}
		else 
		{
			$result = $model->where($condition)->find();
		}
    	
    	if(false == $result)
    	{
    		$this->LogErr("show data failed.the table is ".$model->tableName);
    		$this->errorCode = MispErrorCode::DB_GET_ERROR;
    	}
    	$data['obj'] = $result;
    	$this->ReturnJson($data);
    }
    public function Create()
    {
        $this->LogInfo("create");
        $db = $this->GetModel();
        $obj = $this->GetObj();
        $result = $this->Validator($obj);
        if(SUCCESS != $result)
        {
            $this->errorCode = $result;
            $this->LogErr("validator failed when create ".$db—>tableName);
            $this->LogErr("the error is ".$result);
            $this->ReturnJson();
            return;
        }
        $data = $this->objectToArray($obj);
        $result = $db->add($data);	//$result获取到的是新创建对象的ID
	    if(false == $result)
        {
            $this->LogErr("create data failed.the table is ".$db—>tableName);
            $this->errorCode = MispErrorCode::DB_CREATE_ERROR;
        }
        $this->ReturnJson();
        
    }
    
    public function Modify()
    {
        $this->LogInfo("modify");
        
        $obj = $this->GetObj();
        $db = $this->GetModel();
        $result = $this->Validator($obj);
        if(SUCCESS != $result)
        {
            $this->errorCode = $result;
            $this->LogErr("validator failed when create ".$db->tableName);
            $this->LogErr("the error is ".$result);
            $this->ReturnJson();
            return;
        }    
        
        $data = $this->objectToArray($obj);
        $result = $db->save($data);
        if(false == $result)
        {
            $this->LogErr("modify data failed.the table is ".$db->tableName);
        	$this->errorCode = MispErrorCode::DB_MODIFY_ERROR;
        }
        $this->ReturnJson();
    }
    
    public function Delete()
    {
        $this->LogInfo("delete");
        $obj = $this->GetObj();
        
 
        $db = $this->GetModel();
 
        $result = $db->delete($obj);
    
        if(false == $result)
        {
            $this->LogErr("delete data failed.the table is ".$db->tableName);
        	$this->errorCode = MispErrorCode::DB_DELETE_ERROR;
        }
        $this->ReturnJson();
    }
    
    public function LoadPageTable($model,$condition)
    {
    	$ReqType = $this->GetReqType();
    	if(($ReqType == ClientTypeEnum::IOS)||($ReqType == ClientTypeEnum::ANDROID))
    	{
    		$productList = $model->where($condition)->select();
    		$data['obj'] = $productList;
    		$this->ReturnJson($data);
    	}
    	else 
    	{
    		$count = $model->where($condition)->count();
    		$page = $this->GetPage();
    		$rows = $model->where($condition)->limit($page['currentPage'],$page['pageSize'])->select();
    		$this->LogInfo("query the table ".$model->tableName." count is ".$count);
    		$data['total'] = $count;
    		$data['rows'] = $rows;
    		if(false == $rows)
    		{
    			$this->errorCode = MispErrorCode::DB_GET_ERROR;
    		}
    		$this->ReturnJson($data);
    	}
    }
    
}