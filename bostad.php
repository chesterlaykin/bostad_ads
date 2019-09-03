
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

    //TODO: 1. keep old list, see which results, dating from yesterday and backwards,
    //which is not outdated, which are missing from the latest list.
    //List those under the new ones.
    $page = file_get_contents($url);
    file_put_contents('current_ads.json', $page);

    $page = json_decode($page, true);
    $timeDiffHours = 0;

}else{
    $page = file_get_contents($filename);
    $page = json_decode($page, true);
}
 
//TODO - after below creation of custom list of ads without excludes,
//loop through them and fetch additional info from each page on https://bostad.stockholm.se/
function fetchSaveAdditionalInfo($adsOfTypes){

    foreach($adsOfTypes as $adType){

        // $page = file_get_contents($url);

        /*
            https://medium.com/velotio-perspectives/web-scraping-introduction-best-practices-caveats-9cbf4acc8d0f

            User Agent Rotation and Spoofing: A User-Agent String in the request header helps 
            identify which browser is being used, what version, and on which operating system. 
            Every request made from a web browser contains a user-agent header and using the 
            same user-agent consistently leads to the detection of a bot. User Agent rotation 
            and spoofing is the best solution for this. Spoof the User Agent by creating a list 
            of user agents and picking a random one for each request. Websites do not want to
            block genuine users so you should try to look like one. Set your user-agent to a 
            common web browser instead of using the default user-agent (such as wget/version
            or urllib/version)! If you’re using Scrapy then you can set USER_AGENT property 
            in settings.py. 
            Generally, you can keep the format as: ‘myspidername: myemailaddress’ so that the 
            target website would know it’s a spider and contact address.
        */
        //dont make all requests at the same time (might get blocked)
        usleep(10000000);
    }
}


//common to both korttid and bostadsrätt
$excludedKommuner = [
    'Järfälla','Södertälje','Sigtuna', 'Botkyrka', 'Österåker', 'Upplands Väsby','Västerås', 'Norrtälje'
];

$excludedStadsdelar = [
    'Rinkeby','Tensta','Akalla','Sigtuna', 'Blackeberg','Hässelby Strand',
    'Hässelby Gård','Skärholmen', 'Husby','Rågsved','Fisksätra', 'Hökarängen'
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
    'Solhem'.'Bredäng', 'Bagarmossen','Vällingby','Kista','Fruängen','Bredäng','Mariehäll',
    'Vällingby','Skarpnäcks Gård','Flemingsberg'
];

$excludedKortidsspecificKommuner = array_unique(array_merge($excludedKommuner, $excludedKortidsspecificKommuner));
$excludedKortidsspecificStadsdelar = array_unique(array_merge($excludedStadsdelar, $excludedKortidsspecificStadsdelar));
$excludedBostadsrattspecificKommuner = array_unique(array_merge($excludedKommuner, $excludedBostadsrattspecificKommuner));
$excludedBostadsrattspecificStadsdelar = array_unique(array_merge($excludedStadsdelar , $excludedBostadsrattspecificStadsdelar));

$korttid = showAds($page, 'Korttid', $excludedKortidsspecificKommuner, $excludedKortidsspecificStadsdelar, $now);
$vanlig = showAds($page, 'Vanlig', $excludedBostadsrattspecificKommuner, $excludedBostadsrattspecificStadsdelar, $now);
   
$nyinkomet =( $korttid['nyinkommet'] ||  $vanlig['nyinkommet']) ? true : false;
 

$adsOfTypes = [
    'korttid' => [
        'ads' => $korttid['ads'],
        'excluded_count' => $korttid['excluded_count'],
    ],
    'vanlig' => [
        'ads' => $vanlig['ads'],
        'excluded_count' => $vanlig['excluded_count'],
    ],
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
   
    $excludedCount = 0;

    $nyinkommet = 0;

    $filteredAds = [];
    
    foreach($ads as $ad){        
        
        if($ad[$typeProperty] == 1) {

            //See if the ad should be excluded

            if($ad['AntalRum'] > 3 || in_array($ad['Kommun'], $excludedKommuner) || in_array($ad['Stadsdel'], $excludedStadsdelar)) {
                $excludedCount++;
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
        'nyinkommet' => $nyinkommet,
        'excluded_count' => $excludedCount
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
    <div>
        
        <div id="header-wrap">
            <div id="header">
                <h1>Annonser - <?php echo date("Y/m/d"); ?>  </h1>
                <p>Från Stockholms bostadskö</p>
            </div>
        </div>
        <div class="content">
            <div class="row">
                <div class="size-smaller">Listan är <span class="size-bigger"><?php echo round($timeDiffHours, 2); ?></span> timmar gammal.</div>
                <?php if(!$nyinkomet): ?>

                    <p class="notification">Inget nyinkommet idag</p>
                <?php endif; ?>
            </div>
            <?php if(isset($adsOfTypes) && !empty($adsOfTypes)):  
    
                
                foreach($adsOfTypes as $key => $adsOfType) : 

                    // $exludedCount = ($key == 'korttid') ? $adsOfTypes['excluded_count'] : $adsOfTypes['vanlig']['excluded_count']; 
            
                    $adType = ($key == 'korttid') ? 'Korttidskontrakt' : 'Hyresrätter'; ?>

                    <h2><?php echo $adType . ' ( ' . count($adsOfType['ads']) . ' st )' ?> </h2> 
                    <p class="faded"><?php echo ($adsOfType['excluded_count'] == 1) ? $adsOfType['excluded_count'] . ' bostad är exkluderad.' : $adsOfType['excluded_count'] . ' bostäder är exkluderade.'; ?></p>
                    <div class="adswrap flexctr">
                        <?php foreach($adsOfType['ads'] as $ad) : 
                            //
                            $currentMapUrl = $mapUrl . str_replace(' ','+',$ad['Gatuadress']) . '+' . str_replace(' ','+',$ad['Kommun']);

                            if(  $ad['AnnonseradFran'] == $now){
                                $income_today = 'income_today';
                            }else{
                                $income_today = '';
                            }   ?>
                            <div class="ad <?php echo ($ad['Hyra'] > 7000) ? 'expensive ' : ' '; echo $income_today; ?>">
                                <div class="">
                                    <?php //echo $ad[''] . ', ' . echo $ad['']; ?>

                                    <div class="info1">
                                    
                                        <h2><?php echo $ad['Kommun'] . ', ' . $ad['Stadsdel']; ?> </h2>       
                                        
                                    <a target="_blank" href="<?php echo $domain . $ad['Url'];?>"><i class="fas fa-external-link-alt"></i><?php echo $ad['Gatuadress']; ?></span></a>
                                        <div class="map-link"><a target="_blank" href="<?php echo $currentMapUrl ;?>"><i class="fas fa-external-link-alt"></i><span>Karta</span></a></div>
                                        
                                        <div class="hr hr_first"><hr /></div>
                                        
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

                                    <div class="hr hr_last"><hr /></div>

                                    <div class="infosection flexctr info4">


                                        <?php if($ad['KoNamn'] !== 'Bostadskön') : ?>

                                            <div class="flexctr">
                                                <div>Könamn:</div><div><?php echo $ad['KoNamn']; ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>                
    </div>
 
    <script src="bostad.js"></script>
</body>
</html>

 


