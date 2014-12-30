<?php

class Project extends RestGeneric{
  public static $methods = array(
    'createProject' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "id_model":"1",
                               "id_creator":"1",
                               "project_name" : "Dapper",
                               "project_description" : ""
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'setProjectAuth' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "id_project":"1",
                               "id_user":"1"
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'getProjects' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_user' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_INT,
          'description' => '',
          'exampleValue' => '2'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'deleteProject' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_project' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_INT,
          'description' => '',
          'exampleValue' => '1'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
  );

  function createProject($request){
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $id_model = $action->id_model;
      $id_creator = $action->id_creator;
      $project_name = $action->project_name;
      $project_description = $action->project_description;
      $sql = "INSERT INTO project (id_model,id_creator,project_name, project_description) VALUES ('{$id_model}','{$id_creator}','{$project_name}','{$project_description}');";
      if (!serviceHelper::isExistIn("id_project","project",array(array("key"=>"project_name","value"=>$project_name)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("existing");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }

  function setProjectAuth($request){
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $id_project = $action->id_project;
      $id_user = $action->id_user;
      $sql = "INSERT INTO project_auth (id_project,id_user) VALUES ('{$id_project}','{$id_user}');";
      if (!serviceHelper::isExistIn("*","project_auth",array(array("key"=>"id_project","value"=>$id_project),array("key"=>"id_user","value"=>$id_user)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("existing");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }

  function getProjects($request){
    $id_user = $request['id_user'];
    $user_type = array_pop(serviceHelper::query("SELECT user_type FROM user WHERE id_user = '{$id_user}'"))['user_type'];
    switch ($user_type) {
      case '1':
        $sql = "SELECT * FROM project;";
        break;
      case '2':
        $sql = "SELECT * FROM project p RIGHT JOIN (SELECT DISTINCT id_project FROM project_auth WHERE id_user = '{$id_user}') pa ON pa.id_project = p.id_project;";
        break;
    }
    $projects = serviceHelper::query($sql);
    return $projects;
  }

  function deleteProject($request){
    $id_project = $request['id_project'];
    $sqlArray = array(
      "project" => "DELETE FROM project WHERE id_project = {$id_project};",
      "project_auth" => "DELETE FROM project_auth WHERE id_project = {$id_project};",
      "project_page" => "DELETE FROM project_page WHERE id_project = {$id_project};",
    );
    foreach ($sqlArray as $sql) {
      serviceHelper::query($sql);
    }
    return serviceHelper::returnMessage("success");
  }
}