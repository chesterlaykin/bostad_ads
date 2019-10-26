<?php 

namespace Joels\Bostad;

use App\Http\Controllers\Controller;

class BostadController extends Controller {

  public function __construct() {
    //
  }

  /**
  * Get main view for bostad app
  */
  public function index()
  { 
      

    //getBostadData - se helpers.php
    $dataArray = getBostadData();
    $dataArray['title'] = 'Bostadsförmedlingen - aktuella bostäder';

    $keys = array_keys($dataArray);
   
    extract($dataArray);
    // print_r(compact($keys)); die();

    // return view('bostad', compact($keys));
    return view('joels/bostad::bostad',compact($keys));
  }
}