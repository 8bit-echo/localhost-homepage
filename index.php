<?php 
  # Get Vhosts files
  $path         = '/usr/local/etc/apache2/2.4/vhosts';   // change to match your setup
  $serverPath   = '/Users/vermilion/Sites/';             // ie  '/Users/vermilion/Sites/' include trailing slash
  $tld          = '.dickey.vera';                        // ie '.dickey.vera' include leading "."
  $a_directory  = scandir( $path );
  $a_conf_files = array_diff( $a_directory, array( '..', '.' ) );
  $sites        = [];
  $x            = 0;  
  function getFavicon($url){
    # make the URL simpler
    $elems = parse_url($url);
    $url = $elems['scheme'].'://'.$elems['host'];
    if ($elems['scheme'] === 'https') {
      $arrContextOptions = [
        "ssl"=>[
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ],
      ];
    $context = stream_context_create($arrContextOptions);
    $output = file_get_contents($url, false, $context );
    } else {
      $output = file_get_contents($url);
    }

    # look for the shortcut icon inside the loaded page
    $regex_pattern = "/rel=\"shortcut icon\" (?:href=[\'\"]([^\'\"]+)[\'\"])?/";   
    preg_match_all($regex_pattern, $output, $matches);

    if(isset($matches[1][0])){
        $favicon = $matches[1][0];

        # check if absolute url or relative path
        $favicon_elems = parse_url($favicon);

        # if relative
        if(!isset($favicon_elems['host'])){
            $favicon =  $favicon_elems['path'];
            echo $favicon;
        }

        return $favicon;
    }
    return false;
  }

  foreach ( $a_conf_files as $conf_file ) {
  	$file   = fopen( $path . '/' . $conf_file, 'r' )or die( 'No conf files found' );

  	while ( ! feof( $file ) ) {
  		$line = fgets( $file );
  		$line = trim( $line );

  		$tokens = explode( ' ', $line );

  		if ( ! empty( $tokens ) ) {
        // HTTP ?
        if ( count($tokens) > 1) {
  					if (strtolower( $tokens[1] ) == "*:80>") { 
          		$sites[$x]['ServerType'] = 'http://';
        	}
  			}
        // HTTPS ?
        if ( count($tokens) > 1){
  					if (strtolower( $tokens[1] ) == "*:443>"){
          		$sites[$x]['ServerType'] = 'https://';
        	}
  			}
        //Site Name and URL
  			if ( strtolower( $tokens[0] ) == 'servername' ) {
          if ( $tokens[ 1 ] !== 'localhost' || $tokens[ 1 ] !== $tld ){
    				$sites[ $x ]['ServerName'] = $tokens[ 1 ];
            $sites[ $x ][ 'PrettyName' ] = str_replace( $tld,'', $tokens[ 1 ] );
          }
  			}
        //Location on Machine
  			if ( strtolower( $tokens[0] ) == 'documentroot' ) {
  				$sites[ $x ]['DocumentRoot'] = str_replace('"','',$tokens[1]);
  			}
  		} else {
  			echo 'Couldnt get site data...';
  		}
  	}
  	fclose( $file );
    //URL for later
    $sites[$x]['URL'] = $sites[$x]['ServerType'] . $sites[$x]['ServerName'];
  	$x++;
  }
  
  for ($i=0; $i < count($sites); $i++) { 
    if ($sites[$i]['ServerName'] == 'localhost'|| $sites[$i]['ServerName'] ==  $tld) {
      unset($sites[$i]);
      continue;
    }
  }
  
  // Reindex the sites
  $sites = array_values($sites);

  //Maybe later...
  // $arrContextOptions = [
  //   "ssl"=>[
  //       "verify_peer"=>false,
  //       "verify_peer_name"=>false,
  //   ],
  // ];
// $icon = getFavicon('https://beforeplay.dickey.vera');
// echo '<img src="';
// echo $sites[0]['DocumentRoot'] . $icon;
// echo '">';

//==========================//
//       End BACK-END      //
//=========================//
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Localhost</title>
    <link rel="stylesheet" href="lib/normalize.css">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,700" rel="stylesheet">
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="lib/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="lib/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="lib/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="lib/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="lib/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="lib/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="lib/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="lib/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="lib/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="lib/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="lib/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="lib/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="lib/favicon/favicon-16x16.png">
    <link rel="manifest" href="lib/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="lib/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <style media="screen">
      body{
        font-family: 'Ubuntu', sans-serif;
        background-color: hsl(270, 3%, 89%);
      }
      header{
        text-align: center;
      }
      .container{
        width: 90%;
        margin: 0 auto;
        text-align: center;
      }
      .list{
        padding:0;
        list-style:none;
        display:flex;
        flex-wrap: wrap;
        justify-content: center;
      }
        
      .site-container{
        width: 300px;
        height: 30px;
        padding:15px 0;
        margin: 1em;
        background-color: white;
        box-shadow: 4px 4px 3px rgba(0,0,0,0.5);
        display:flex;
        justify-content: space-between;
        align-items: center;
        transition: .2s ease;
      }
      
      .site-container:hover{
        box-shadow: 2px 2px 1px rgba(0,0,0,0.5);
        transition: .2s ease;
      }
      a,
      .site-container span {
        font-size: 14px;
        padding-bottom: 1.5px;
        text-decoration: none;
        font-weight: bold;
        text-align: center;
        color:black;
        padding-left: 1em;
        text-transform: uppercase;
        letter-spacing: 1.5px;
      }
      
      .search-container{
        display: flex;
        width: 40%;
        margin: 0 auto;
        align-items: baseline;
      }
      
      .search {
        width: 100%;
        padding: 0 2em;
        text-align: center;
        background: transparent;
        border: none;
        border-bottom: solid 3px grey;
        outline: none;
        transition: ease .5s;
        font-size: 2em;
      }
      
      .search:focus {
        border-bottom-color: #008DBA;
        transition: ease .5s;
      }
      
      .cancel{
        position: relative;
        right: 30px;
        bottom: 3px;
        font-size: 1.4em;
        background: rgba(192, 192, 192, 0.6);
        color: #e3e2e4;
        cursor: pointer;
        padding: 3px 9px;
        border-radius: 50%;
      }
      .cancel:hover{
        color: darkgrey;
      }
      
      .hide{
        display: none;
      }
      </style>
  </head>
  <body>
    
    <header>
      <h1>Localhost Root</h1>
      <h2>You've reached the Server Root at "<?php echo $serverPath;?>"</h2>
    </header>
    <main class="container">
      <div id="sites">
        <div class="search-container">
          <input type="search" ref="search" v-model="search" class="search" autofocus>
          <div class="cancel" :class="{hide : isHidden}" @click="clearInput">&times;</div>
        </div>
        <ul class="list">          
          <!-- VUE APP -->
          <li v-for="site in filteredList">
            <a :href="site.URL" target="_blank" class="site-container">
              <span>{{site.PrettyName}}</span>
              <img :src="getImgPath()">
            </a>
          </li>
        </ul>
      </div>
    </main>
    
    <script src="lib/vue.js"></script>    
    <script type="text/javascript">
    
      var sites = <?php echo json_encode($sites);?>;
      sites.unshift({
        PrettyName: 'phpinfo()',
        URL: 'info.php',
        imagePath:'lib/php.png'
      });  
      var app = new Vue({
        el: '#sites',
        data: {
          search: '',
          isHidden: true,
          sites: sites,
          getImgPath: function(){
            // TODO: write function to grab favicon
            var imgFound = this.sites.icon;
            if (!imgFound){
              return 'lib/wp-icon.png';
            } else {
              return this.sites.icon;
            }
          },
        },
        computed: {
          filteredList(){
            return this.sites.filter(site => {return site.PrettyName.toLowerCase().includes(this.search.toLowerCase())})
          }
        },
        methods:{
          clearInput(){
            this.search = '';
          }
        },
        watch:{
          search: function() {            
            this.isHidden = (this.search === '');
          }
        }
      });
      document.getElementsByClassName('search')[0].focus();
    </script>
  </body>
</html>