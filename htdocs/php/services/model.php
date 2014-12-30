<?php

class Model extends RestGeneric{
  public static $methods = array(
    'createModel' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "model_name":"Musee",
                               "model_description":"A model designed for our museum application",
                               "model_image" : ""
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'getModels' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => false,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => ''
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'updateModel' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "id_model" : "1",
                               "model_name":"Musee Updated",
                               "model_description":"A model designed for our museum application Updated",
                               "model_image" : "images/musee.jpg"
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'deleteModel' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_model' => array(
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
    'createPattern' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "id_model":"1",
                               "pattern_name":"Homepage",
                               "pattern_description":"Pattern description",
                               "pattern_detail":""
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'getPatterns' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_model' => array(
          'required' => false,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => 'Get all patterns with id of the model',
          'exampleValue' => '1'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'updatePattern' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'action' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_STRING,
          'description' => '',
          'exampleValue' => '{ "id_model":"1",
                               "id_pattern":"1",
                               "pattern_name":"Homepage updated",
                               "pattern_description":"Pattern description updated",
                               "pattern_detail":{
                                "color" : "red"
                               }
                             }'
        )
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'deletePattern' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_model' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_INT,
          'description' => '',
          'exampleValue' => '1'
        ),
        'id_pattern' => array(
          'required' => ture,
          'type' => RestGeneric::PARAM_TYPE_INT,
          'description' => '',
          'exampleValue' => '1'
        ),
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
    'getGeneralComponents' => array(
      'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
      'description' => '',
      'params' => array(
        'id_model' => array(
          'required' => false,
          'type' => RestGeneric::PARAM_TYPE_INT,
          'description' => '',
          'exampleValue' => '1'
        ),
      ),
      'returnExample' => array(
        'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
        'value' => '{"status":"ok","time":1356012312,"exectime":0.181,"response":{}}'
      )
    ),
  );
  /**
   * [Model description:user as airweb can CRUD a model]
   * @param  [array] $request [request / data get by REST API, Json named action]
   * @return [array]          [info / data]
   */
  function createModel($request){
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $model_name = $action->model_name;
      $model_description = $action->model_description;
      $model_image = $action->model_image;
      $sql = "INSERT INTO model (model_name,model_description,model_image) VALUES ('{$model_name}','{$model_description}','{$model_image}');";
      if (!serviceHelper::isExistIn("id_model","model",array(array("key"=>"model_name","value"=>$model_name)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("existing");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }

  function getModels($request){
    $models = serviceHelper::query("SELECT * FROM model");
    return $models;
  }

  function updateModel($request){
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $id_model = $action->id_model;
      $model_name = $action->model_name;
      $model_description = $action->model_description;
      $model_image = $action->model_image;
      $sql = "UPDATE model SET model_name = '{$model_name}' , model_description = '{$model_description}' , model_image = '{$model_image}' WHERE id_model = '{$id_model}';";
      if ($result = serviceHelper::isExistIn("id_model","model",array(array("key"=>"id_model","value"=>$id_model)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("inexisting");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }
  //TODO delete all project reference to the model
  function deleteModel($request){
    $id_model = $request['id_model'];
    $sqlArray = array();
    $sql = "DELETE FROM model WHERE id_model = '{$id_model}';";
    serviceHelper::query($sql);
    return serviceHelper::returnMessage("success");
  }
  /**
   * [Pattern description:user as airweb can several patterns for one model]
   * @param  [array] $request [request / data get by REST API, Json named action]
   * @return [array]          [info / data]
   */
  function createPattern($request){
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $id_model = $action->id_model;
      $pattern_name = $action->pattern_name;
      $pattern_description = $action->pattern_description;
      $pattern_detail = is_object($action->pattern_detail) ? json_encode($action->pattern_detail) : $action->pattern_detail;
      $sql = "INSERT INTO pattern (id_model,pattern_name,pattern_description,pattern_detail) VALUES ('{$id_model}','{$pattern_name}','{$pattern_description}','{$pattern_detail}');";
      if (!serviceHelper::isExistIn("id_pattern","pattern",array(array("key"=>"pattern_name","value"=>$pattern_name),array("key"=>"id_model","value"=>$id_model)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("existing");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }

  function getPatterns($request){
    $id_model = $request['id_model'];
    $patterns = serviceHelper::query("SELECT * FROM pattern WHERE id_model = '{$id_model}';");
    return $patterns;
  }

  function updatePattern($request){
          $action = json_decode($request['action']);
          var_dump($action);
    if (serviceHelper::isValidRequest($request['action'])) {
      $action = json_decode($request['action']);
      if ($action == null) { return serviceHelper::returnMessage("missing");}
      $id_model = $action->id_model;
      $id_pattern = $action->id_pattern;
      $pattern_name = $action->pattern_name;
      $pattern_description = $action->pattern_description;
      $pattern_detail = is_object($action->pattern_detail) ? json_encode($action->pattern_detail) : $action->pattern_detail;
      $sql = "UPDATE pattern SET pattern_name = '{$pattern_name}', pattern_description ='$pattern_description', pattern_detail ='$pattern_detail' WHERE id_model = '{$id_model}' AND id_pattern = '{$id_pattern}';";
      if ($result = serviceHelper::isExistIn("id_pattern","pattern",array(array("key"=>"id_model","value"=>$id_model),array("key"=>"id_pattern","value"=>$id_pattern)))) {
        serviceHelper::query($sql);
        return serviceHelper::returnMessage("success");
      }else{
        return serviceHelper::returnMessage("inexisting");
      }
    }else{
      return serviceHelper::returnMessage("missing");
    }
  }

  function deletePattern($request){
    $id_model = $request['id_model'];
    $id_pattern = $request['id_pattern'];
    $sql = "DELETE FROM pattern WHERE id_pattern = '{$id_pattern}' AND id_model = '{$id_model}';";
    serviceHelper::query($sql);
    return serviceHelper::returnMessage("success");
  }

  function getGeneralComponents($request){
    $sql = "SELECT * FROM component_general;";
    return serviceHelper::query($sql);
  }
}