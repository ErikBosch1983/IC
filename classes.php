<?php
# Made by Mitch Vroege 2005
# Versie 0.6.2

# Class database
class database
	{
		# Database connectie variabele
		var $db;
		
		# Constructor
		function database( $user, $pass, $database, $error = 'MySQL database niet bereikbaar!', $host = 'localhost' )
			{
				# Connectie leggen
				$this->db = mysql_connect( $host, $user, $pass );
				
				# Kijken of de connectie gelukt is, en of de database geselecteerd kon worden        
				if( !$this->db || !mysql_select_db( $database, $this->db ) )
					die( $error );
			}
		
		# Query resultaat variabele
		var $res;
		
		# Functie die mysql_num_rows teruggeeft
		function num_rows( $sql = NULL )
			{
				# Kijken of er een query meegegeven is, zo ja uitvoeren
				if( $sql != NULL )
					$this->run_query( $sql );
				
				# Mysql_num_rows uitspugen
				return mysql_num_rows( $this->res );
			}
		
		# Functie die mysql_fetch_array teruggeeft
		function get_array( $sql = NULL, $refetch = false )
			{
				# Kijken of er een query meegegeven is, zo ja uitvoeren
				if( $sql != NULL )
					$this->run_query( $sql );
					
				# Kijken of refetch is meegegeven
				if( $refetch == true )
					mysql_data_seek( $this->res, 0 );    
				
				# Loopje door alle resultaten
				while( $row = mysql_fetch_array( $this->res ) )
					$rows[] = $row;
				
				# Array met rows uitspugen
				return $rows;
			}
		
		# Functie die mysql_insert_id teruggeeft
		function insert_id( $sql = NULL )
			{
				# Kijken of er een query meegegeven is, zo ja uitvoeren
				if( $sql != NULL )
					$this->run_query( $sql );
					
				# Mysql_insert_id uitspugen
				return mysql_insert_id( $this->db );
			}
		
		# Functie die mysql_affected_rows teruggeeft
		function affected_rows( $sql = NULL )
			{
				# Kijken of er een query meegegeven is, zo ja uitvoeren
				if( $sql != NULL )
					$this->run_query( $sql );
					
				# Mysql_affected_rows uitspugen
				return mysql_affected_rows( $this->db );
			}
		
		# Functie die kijkt of $table in $database bestaat, zoja 'true', zo nee, 'false'
		function find_table( $table, $database )
			{
				# Tabellen opvragen
				$this->res = mysql_list_tables( $database, $this->db );
				
				# Tabellen in een array proppen
				$data = $this->get_array( );
				
				# Return true of false
				return in_array( $table, $data );
			}
			
		# Functie die de query uitvoert
		function run_query( $sql )
			{
				# Query uitvoeren en het resultaat wegschrijven in de $res var
				$this->res = mysql_query( $sql, $this->db );
			
				# Kijken of het een geldige query is
				if( !$this->res )
					die( 'De opgegeven query is onjuist!' . $sql );
			}
		
		# Functie die de verbinding sluit & $res leegt
		function database_close( )
			{
				# $res legen
				if( !@mysql_free_result( $this->res ) )
					unset( $this->res );
				
				# Verbinding sluiten
				mysql_close( $this->db );
			}
	}

# Class database destructor
function destruct( $name )
	{
		# Referentie naar object $name meegeven aan $db
		$db = &$GLOBALS[$name];
		# Database afsluiten
		$db->database_close( );
		# Object verwijderen
		unset( $GLOBALS[$name] );
	}
?>