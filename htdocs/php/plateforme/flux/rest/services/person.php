<?php
/*
 * Exemple d'utilisation: la classe person etend RestGeneric, et contient 3 méthodes : get, add et delete
 */
class person extends RestGeneric{

    public static $methods = array(
        'get' => array(
            //Types d'appels autorisés
            'type' => array(RestGeneric::METHOD_GET),
            //Description de la méthode
            'description' => 'Récupération des personnes',
            //Paramètres
            'params' => array(),
            //Exemple de retour (auto généré)
            'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_DYNAMIC)
            ),
        'add' => array(
            'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST),
            'description' => 'Ajout d\'une personne',
            'params' => array(
                //Paramètre obligatoire
                'firstname' => array('required' => true, 'type' => RestGeneric::PARAM_TYPE_STRING, 'description' => 'Prénom de la personne', 'exampleValue' => 'Toto'),
                //Paramètre facultatif (avec assignation à la valeur "Toto", lorsqu'il n'est pas renseigné)
                'lastname' => array('required' => false, 'defaultValue' => 'Toto', 'type' => RestGeneric::PARAM_TYPE_STRING, 'description' => 'Nom de la personne', 'exampleValue' => 'Tata'),
                ),
            'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_STATIC, 'value' => '{"addStatus":"ok"}')
            ),
        'delete' => array(
            'type' => array(RestGeneric::METHOD_GET, RestGeneric::METHOD_POST, RestGeneric::METHOD_PUT, RestGeneric::METHOD_DELETE),
            'description' => 'Suppression d\'une personne',
            'params' => array(
                'firstname' => array('required' => true, 'type' => RestGeneric::PARAM_TYPE_STRING, 'description' => 'Prénom de la personne', 'exampleValue' => 'Toto'),
                'lastname' => array('required' => true, 'type' => RestGeneric::PARAM_TYPE_STRING, 'description' => 'Nom de la personne', 'exampleValue' => 'Tata'),
                ),
            'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_DYNAMIC)
            ),
        );

    function get($request){
        if (!isset($_SESSION['people'])){
            $_SESSION['people'] = array();
        }

        return array('people' => $_SESSION['people']);
    }

    function add($request){
        if (!isset($_SESSION['people'])){
            $_SESSION['people'] = array();
        }
        $_SESSION['people'][] = array('firstname' => $request['firstname'], 'lastname' => $request['lastname']);

        return array('addStatus' => 'ok');
    }

    function delete($request){
        if (!isset($_SESSION['people'])){
            $_SESSION['people'] = array();
        }
        for ($i=count($_SESSION['people'])-1;$i>=0;$i--){
            if ($_SESSION['people'][$i]['firstname'] == $request['firstname'] && $_SESSION['people'][$i]['lastname'] == $request['lastname']){
                unset($_SESSION['people'][$i]);
            }
        }
        return array('removeStatus' => 'ok');
    }

}