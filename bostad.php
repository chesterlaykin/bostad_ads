
<?php
 
$url = 'https://bostad.stockholm.se/Lista/AllaAnnonser';


//$page = file_get_contents($url);
//file_put_contents('current_ads.json', $page);


/* 
 * typer av bostäder:
 *      - Korttid
 *      - Vanlig
 */
 
/*
 *  All properties of each ad:
 
    [AnnonsId] => 156963
    [Stadsdel] => Mariehäll
    [Gatuadress] => Tappvägen 10
    [Kommun] => Stockholm
    [Vaning] => 4
    [AntalRum] => 2
    [Yta] => 51
    [Hyra] => 9591
    [AnnonseradTill] => 2019-08-04
    [AnnonseradFran] => 2019-08-01
    [KoordinatLongitud] => 17.950459963889
    [KoordinatLatitud] => 59.361831226131
    [Url] => /Lista/Details/?aid=156963
    [Antal] => 1
    [Balkong] => 1
    [Hiss] => 1
    [Nyproduktion] => 1
    [Ungdom] => 
    [Student] => 
    [Senior] => 
    [Korttid] => 
    [Vanlig] => 1
    [Bostadssnabben] => 
    [Ko] => Bostadskön
    [KoNamn] => Bostadskön
    [Lagenhetstyp] => Hyresrätt
    [HarAnmaltIntresse] => 
    [KanAnmalaIntresse] => 
    [HarBraChans] => 
    [HarInternko] => 
    [Internko] => 
    [Externko] => 
    [Omraden] => Array
        (
            [0] => stdClass Object
                (
                    [Id] => 61
                    [PlatsTyp] => 2
                )
 */

//See if
$from = '2019-08-03';
// $now = date('Y-m-d', strtotime($from));
$now = date('Y-m-d',time());

// if($from == $now){
//     echo "equal";
// }else{
//     echo "not equal";
// }

// die();

$today = time();

$filename = 'current_ads.json';

 //check when the file was saved
$lastSavedime = filemtime($filename);
$timeDiffHours = (time() - $lastSavedime) / 3600;

//if saved file is older than a specific amount of hours, fetch a new list
if($timeDiffHours > 10) {

    $page = file_get_contents($url);
    file_put_contents('current_ads.json', $page);

    $page = json_decode($page, true);
    $timeDiffHours = 0;

}else{
    $page = file_get_contents($filename);
    $page = json_decode($page, true);
}
 

//common to both korttid and bostadsrätt
$excludedKommuner = [
    'Järfälla','Södertälje'
];

$excludedStadsdelar = [
    'Rinkeby','Tensta','Akalla','Sigtuna', 'Blackeberg'
];

//specific to korttid and bostadsrätt
$excludedKortidsspecificKommuner = [
    ''
];
$excludedKortidsspecificStadsdelar = [
    ''
];

$excludedBostadsrattspecificKommuner = [
    ''
];
$excludedBostadsrattspecificStadsdelar = [
    ''
];

$excludedKortidsspecificKommuner = array_unique(array_merge($excludedKommuner, $excludedKortidsspecificKommuner));
$excludedKortidsspecificStadsdelar = array_unique(array_merge($excludedStadsdelar, $excludedKortidsspecificStadsdelar));
$excludedBostadsrattspecificKommuner = array_unique(array_merge($excludedKommuner, $excludedBostadsrattspecificKommuner));
$excludedBostadsrattspecificStadsdelar = array_unique(array_merge($excludedStadsdelar , $excludedBostadsrattspecificStadsdelar));

$korttid = showAds($page, 'Korttid', $excludedKortidsspecificKommuner, $excludedKortidsspecificStadsdelar, $now);
$vanlig = showAds($page, 'Vanlig', $excludedBostadsrattspecificKommuner, $excludedBostadsrattspecificStadsdelar, $now);
   
$nyinkomet =( $korttid['nyinkommet'] ||  $vanlig['nyinkommet']) ? true : false;
 

$adsOfTypes = [
    'korttid' => $korttid['ads'],
    'vanlig' => $vanlig['ads']
];

$domain = 'https://bostad.stockholm.se';

$mapUrl = 'https://www.google.com/maps/search/';




/*
 * Lista korttid
 * Lista vanlig
 * 
 * properties:
 *      titel:
 *      
 *      Kommun, Stadsdel, Url, Gatuadress
 *      
 *      info 2:
 *      AntalRum, Yta, Hyra, Balkong
 *       
 *      info 3:
 *      AnnonseradFran, AnnonseradTill
 * 
 *      info 4:
 *      Nyproduktion, AnnonsId, KoNamn
 *      
 */
 
//get korttid

function showAds( $ads, $typeProperty,  $excludedKommuner, $excludedStadsdelar,$now ){
   
    $nyinkommet = 0;

    $filteredAds = [];
    
    foreach($ads as $ad){               //invalid arg suppl for foreach
        
        if($ad[$typeProperty] == 1){
            //See if the ad should be excluded
            if($ad['AntalRum'] > 3 || in_array($ad['Kommun'], $excludedKommuner) || in_array($ad['Stadsdel'], $excludedStadsdelar)){
                continue;
            } 
            if($ad['AnnonseradFran'] == $now){
                $nyinkommet = 1;
            }
            
            $filteredAds[] = $ad;
        }
    }

    //sort by date, latest first
    usort($filteredAds, function($a, $b) {
    
        if (strtotime($a['AnnonseradFran']) == strtotime($b['AnnonseradFran'])) {
            return 0;
        }
        return strtotime($a['AnnonseradFran']) > strtotime($b['AnnonseradFran']) ? -1 : 1;	
    });

    return [
        'ads' => $filteredAds,
        'nyinkommet' => $nyinkommet
    ];
     
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Dosis&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <title>Bostadsförmedlingen - aktuella bostäder</title>
</head>
<body>
    <div class="homesymbol"><i class="fas fa-home"></div></i>
    <div class="content">
        
        <div id="header">
            <h1>Annonser - <?php echo date("Y/m/d"); ?>  </h1>
            <div class="size-smaller">Listan är <span class="size-bigger"><?php echo round($timeDiffHours, 2); ?></span> timmar gammal.</div>
            <?php if(!$nyinkomet): ?>

                <p class="notification">Inget nyinkommet idag</p>
            <?php endif; ?>
        </div>

        <?php if(isset($adsOfTypes) && !empty($adsOfTypes)):  
 
            foreach($adsOfTypes as $key => $adsOfType) : 
                 
                $adType = ($key == 'korttid') ? 'Korttidskontrakt' : 'Hyresrätter'; ?>

                <h2><?php echo $adType . ' ( ' . count($adsOfType) . ' st )' ?> </h2> 

                <div class="adswrap flexctr">
                    <?php foreach($adsOfType as $ad) : 
                        //
                        $currentMapUrl = $mapUrl . str_replace(' ','+',$ad['Gatuadress']) . '+' . str_replace(' ','+',$ad['Kommun']);

                        if(  $ad['AnnonseradFran'] == $now){
                            $income_today = 'income_today';
                        }else{
                            $income_today = '';
                        }   ?>
                        
                        <div class="ad <?php echo ($ad['Hyra'] > 7000) ? 'expensive ' : ' '; echo $income_today; ?>">
                            <?php //echo $ad[''] . ', ' . echo $ad['']; ?>

                            <div class="info1">
                            
                                <h2><?php echo $ad['Kommun'] . ', ' . $ad['Stadsdel']; ?> </h2>       
                                
                               <a target="_blank" href="<?php echo $domain . $ad['Url'];?>"><i class="fas fa-external-link-alt"></i><?php echo $ad['Gatuadress']; ?></span></a>
                                <div class="map-link"><a target="_blank" href="<?php echo $currentMapUrl ;?>"><i class="fas fa-external-link-alt"></i><span>Karta</span></a></div>
                                 
                                <div style="padding: 0 20%"><hr /></div>
                                
                            </div>

                            <div class="infosection flexctr info2">
                                <?php if($ad['Antal'] > 1): ?>
                                <div class="flexctr">
                                    <div><strong><?php echo $ad['Antal'] . ' lägenheter'; ?></strong></div>
                                </div>
                                <?php endif;
                                if($ad['Nyproduktion']): ?>
                                        <div class="flexctr">
                                            <div><strong>Nyproduktion</strong></div>
                                        </div>
                                <?php endif; ?>
                                <div class="flexctr">
                                    <div>Antal rum</div><div class="size-bigger"> <?php echo $ad['AntalRum']; ?></div>
                                </div>
                                <?php if($ad['Antal'] == 1): ?>
                                    <div class="flexctr">
                                        <div>Yta</div><div class="size-bigger"> <?php echo $ad['Yta']; ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="flexctr">
                                    <div>Våningsplan</div><div class="size-bigger"> <?php echo $ad['Vaning']; ?></div>
                                </div>
                                <div class="flexctr">
                                    <div>Hyra</div><div class="size-bigger"> <?php echo $ad['Hyra']; ?> kr</div>
                                </div>
                                <?php if($ad['Balkong']): ?>
                                    <div class="flexctr">
                                        <div>Balkong</div>
                                    </div>
                                 <?php endif; ?>
                            </div>
                            
                            <div class="infosection flexctr info3">
                                <div class="flexctr">
                                    <div>Annonserad från:</div><div> <?php echo $ad['AnnonseradFran']; ?></div>
                                </div>
                                <div class="flexctr">
                                    <div>Annonserad till</div><div> <?php echo $ad['AnnonseradTill']; ?></div>
                                </div>
                            </div>
                            
                                <div class="infosection flexctr info4">

                                    <?php if($ad['KoNamn'] !== 'Bostadskön') : ?>

                                        <div class="flexctr">
                                            <div>Könamn:</div><div><?php echo $ad['KoNamn']; ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            
                           
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
 

</body>
</html>

 


