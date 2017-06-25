# Apache Localhost Homepage

This is a simple landing page to replace the default Apache webpage on your localhost server. This is useful for machines that have many development sites at one time.

## Features
  - Made with VueJS
  - Simple, Clean, Fast.
  - Searchable List

## Usage
  1. Download this repo and move its contents to the DocumentRoot of your localhost server. Mine lives at /Users/username/Sites.
  2. Locate the vhosts folder on your machine. This site parses through individual .conf files to generate the list items.
  3. Open index.php and edit the lines at the very top of the script to point to your vhost parent directory, your ServerRoot, and if necessary, your custom top level domain name (i.e. ".dev").
  4. go to your browser and open "http://localhost" to see your new landing page.

## Coming Eventually
  - Get the favicon for each site (Currently defaults to a WordPress Icon).