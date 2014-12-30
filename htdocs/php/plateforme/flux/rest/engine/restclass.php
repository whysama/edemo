<?php

class RestGeneric {

  const METHOD_GET = 1;
  const METHOD_POST = 2;
  const METHOD_PUT = 3;
  const METHOD_DELETE = 4;

  const PARAM_TYPE_INT = 1;
  const PARAM_TYPE_FLOAT = 2;
  const PARAM_TYPE_STRING = 3;
  const PARAM_TYPE_MIXED = 4;

  const RETURN_EXAMPLE_STATIC = 1;
  const RETURN_EXAMPLE_DYNAMIC = 2;

  public static $methods = array(
        /*'function_name' => array(
            'type' => array(RestGeneric::METHOD_GET),
            'description' => 'function description',
            'params' => array(
               'param_name' => array('required' => true|false, 'type' => RestGeneric::PARAM_TYPE_INT, 'description' => 'parameter description', 'exampleValue' => 0)
             ),
            'returnExample' => array(
               'type' => RestGeneric::RETURN_EXAMPLE_STATIC,
               'value' => '{"result":0}'
            )
           )*/
        );

  /*
   * FUNCTION EXAMPLE
   */
  /*
  function function_name($request){
    //USAGE of $request['param_name'];

    return array('result' => $request['param_name']);
  }
   */

}