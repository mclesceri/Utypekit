<?php
error_reporting( E_ERROR | E_PARSE );
add_action( 'init', 'cp_calculatedfieldsf_init_ds', 11 );

function cp_calculatedfieldsf_init_ds()
	{
		if( isset( $_REQUEST[ 'cffaction' ] ) )
		{
			$_REQUEST = stripslashes_deep( $_REQUEST );
			switch( $_REQUEST[ 'cffaction' ] )
			{
				case 'test_db_connection':
					global $cpcff_db_connect;

					$_REQUEST[ 'data_source' ] = 'database';
					$_REQUEST[ 'query' ] = 'SHOW tables';
					$result =  cp_calculatedfieldsf_ds( $_REQUEST );
					$err = mysqli_error( $cpcff_db_connect );
					if( !is_null( mysqli_connect_error() ) ) $err .= mysqli_connect_error();
					print( ( ( empty( $err ) ) ? 'Connection OK' : $err ) );
					exit;
				break;
				case 'test_db_query':
				    if( $_REQUEST[ 'active' ] == 'structure' )
					{
						_cp_calculatedfieldsf_check_for_variable( $_REQUEST[ 'table' ] );
						_cp_calculatedfieldsf_check_for_variable( $_REQUEST[ 'where' ] );
					}
					else
					{
						_cp_calculatedfieldsf_check_for_variable( $_REQUEST[ 'query' ] );
					}
				case 'get_data_from_database':
					global $cpcff_db_connect;

					$_REQUEST[ 'data_source' ] = 'database';
					if( $_REQUEST[ 'active' ] == 'structure' )
					{
						$_REQUEST[ 'query' ] = '';
					}

					$query_result =  cp_calculatedfieldsf_ds( $_REQUEST );
					$err = mysqli_error( $cpcff_db_connect );
					if( !is_null( mysqli_connect_error() ) ) $err .= mysqli_connect_error();
					if( $_REQUEST[ 'cffaction' ] == test_db_query )
					{
						print_r( ( ( empty( $err ) ) ? $query_result : $err ) );
					}
					else
					{
						$result_obj = new stdClass;
						if( !empty( $err ) )
						{
							$result_obj->error = $err;
						}
						else
						{
							$result_obj->data = $query_result;
						}
						print( json_encode( $result_obj ) );
					}
					exit;
				break;
				case 'get_post_types':
					print json_encode(  get_post_types( array( 'public' => true ) ) );
					exit;
				break;
				case 'get_posts':
					$_REQUEST[ 'data_source' ] = 'post_type';
					$result_obj = new stdClass;
					$result_obj->data = cp_calculatedfieldsf_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
				case 'get_available_taxonomies':
					print json_encode( get_taxonomies( array('public' => true), 'objects' ) );
					exit;
				break;
				case 'get_taxonomies':
					$_REQUEST[ 'data_source' ] = 'taxonomy';
					$result_obj = new stdClass;
					$result_obj->data = cp_calculatedfieldsf_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
				case 'get_users':
					$_REQUEST[ 'data_source' ] = 'user';
					$result_obj = new stdClass;
					$result_obj->data = cp_calculatedfieldsf_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
				case 'get_csv_headers':
					if( is_admin() )
					{
						$_REQUEST[ 'data_source' ] = 'csv';
						$_REQUEST[ 'return' ] = 'headers';
						cp_calculatedfieldsf_ds( $_REQUEST );
						exit;
					}
				break;
				case 'get_csv_rows':
					$_REQUEST[ 'data_source' ] = 'csv';
					$_REQUEST[ 'return' ] = 'rows';
					cp_calculatedfieldsf_ds( $_REQUEST );
					exit;
				break;

			}
		}

	} // End cp_calculatedfieldsf_init_ds

function cp_calculatedfieldsf_ds( $data )
	{
		switch( $data[ 'data_source' ] )
		{
			case 'database':
				return cp_calculatedfieldsf_ds_db( $data );
			break;
			case 'csv':
				return cp_calculatedfieldsf_ds_csv( $data );
			break;
			case 'post_type':
				return cp_calculatedfieldsf_ds_post_type( $data );
			break;
			case 'taxonomy':
				return cp_calculatedfieldsf_ds_taxonomy( $data );
			break;
			case 'user':
				return cp_calculatedfieldsf_ds_user( $data );
			break;
		}
	}

/**
	Displays a text about the existence of variables in the query, and stops the script execution.
**/
function _cp_calculatedfieldsf_check_for_variable( $str )
	{
		if( preg_match( '/<%[^%]+%>/', $str ) )
		{
			print 'Your query includes variables, so it cannot be tested from the form\'s edition';
			exit;
		}
	}

/**
	Replace variables from the string
**/
function _cp_calculatedfieldsf_replace_variables( $str, $vars, $is_query = false )
	{
		global $wpdb;
		if( $is_query )
		{
			$str = str_replace( array( '%', '<%', '%>' ), array( '%%', '<', '>' ), $str );
		}
		foreach( $vars as $var => $val )
		{

			$var = '<%'.urldecode( $var ).'%>';
			$val = stripcslashes( $val );
			if( $is_query && !is_numeric( $val ) )
			{
				$str = str_replace( $var, $wpdb->_escape( $val ), $str );
			}
			else
			{
				$str = str_replace( $var, $val, $str );
			}
		}
		return ( is_numeric( $str ) ) ? $str*1 : $str;
	}

function _cp_calculatedfieldsf_set_attr( &$obj, $attr, $arr, $elem )
	{
		$arr = (array)$arr;
		if( !empty( $elem ) && !empty( $arr[ $elem ] ) )
		{
			$tmp = (array)$obj;
			$tmp[ $attr ] = $arr[ $elem ];
			$obj = (object)$tmp;
		}
	}

function cp_calculatedfieldsf_ds_db( $data )
	{
		try
		{
			global $wpdb, $cpcff_db_connect;

			if( !is_admin() || !empty( $data[ 'form' ] ) && !empty( $data[ 'field' ] ) )
			{
				if( empty( $data[ 'form' ] ) && empty( $data[ 'field' ] ) ) return false;
				$obj = get_transient(  'cpcff_db_'.$data[ 'form' ].'_'.$data[ 'field' ] );
				if( $obj === false ) return false;
				// Connection data
				$data[ 'host' ] 		= $obj->databaseData->host;
				$data[ 'user' ] 		= $obj->databaseData->user;
				$data[ 'pass' ] 		= $obj->databaseData->pass;
				$data[ 'database' ] = $obj->databaseData->database;
				// Query data
				$data[ 'query' ] 		= $obj->queryData->query;
				$data[ 'value' ] 		= $obj->queryData->value;
				$data[ 'text' ] 		= $obj->queryData->text;
				$data[ 'table' ] 	= $obj->queryData->table;
				$data[ 'where' ] 	= $obj->queryData->where;
				$data[ 'orderby' ] 	= $obj->queryData->orderby;
				$data[ 'limit' ] 		= $obj->queryData->limit;
			}

			if( !empty( $data[ 'query' ] ) )
			{
				$query = $data[ 'query' ];
			}
			else
			{
				$separator = '';
				$select = '';
				if( !empty( $data[ 'value' ] ) )
				{
					$separator = ',';
					$select .= $data[ 'value' ] . ' AS value';
				}

				if( !empty( $data[ 'text' ] ) )
				{
					$select .= $separator . $data[ 'text' ] . ' AS text';
				}

				$query = 'SELECT DISTINCT ' . $select . ' FROM ' . $data[ 'table' ] . ( ( !empty( $data[ 'where' ] ) ) ? ' WHERE ' . $data[ 'where' ] : '' ) . ( ( !empty( $data[ 'orderby' ] ) ) ? ' ORDER BY ' . $data[ 'orderby' ] : '' ).( ( !empty( $data[ 'limit' ] ) ) ? ' LIMIT ' . $data[ 'limit' ] : '' );
			}

			// Replace variables on query
			$vars = ( !empty( $data[ 'vars' ] )  && is_array( $data[ 'vars' ] ) ) ? $data[ 'vars' ] : array();
			$query = _cp_calculatedfieldsf_replace_variables( $query, $data[ 'vars' ], true );

			if( !empty( $data[ 'host' ] ) ) // External database
			{
				$results = array();
				$cpcff_db_connect = mysqli_connect( $data[ 'host' ], $data[ 'user' ], $data[ 'pass' ], $data[ 'database' ] );

				if( $cpcff_db_connect !== false )
				{
					$query_result = mysqli_query( $cpcff_db_connect, $query );
					while( $query_result && $row = mysqli_fetch_object( $query_result ) )
					{
						$row = (array)$row;
						foreach( $row as $_key => $_val )
						{
							$row[ $_key ] = utf8_encode( $_val );
						}
						$results[] = (object)$row;
					}
				}
				return $results;
			}
			else // Local database
			{
				return $wpdb->get_results( $query, ARRAY_A );
			}
		}
		catch( Exception $err )
		{
			return false;
		}
	} // End cp_calculatedfieldsf_ds_db

function cp_calculatedfieldsf_ds_csv_correct_body( $response, $agrs, $url )
	{
		try
		{
			$response_object = $response[ 'http_response' ]->get_response_object();
			$raw = $response_object->raw;
			if (($pos = strpos($raw, "\r\n\r\n")) === false) return $response;
			$response['body'] = substr($raw, $pos + strlen("\n\r\n\r"));
		}
		catch( Exception $err )
		{
			return $response;
		}
		return $response;
	} // End cp_calculatedfieldsf_ds_csv_correct_body

function cp_calculatedfieldsf_ds_csv( $data )
	{
		$return_obj = new stdClass;
		try
		{
			if( !is_admin() || !empty( $data[ 'form' ] ) && !empty( $data[ 'field' ] ) )
			{
				if( empty( $data[ 'form' ] ) && empty( $data[ 'field' ] ) ) return false;
				$obj = get_transient(  'cpcff_db_'.$data[ 'form' ].'_'.$data[ 'field' ] );
				if( $obj === false ) return false;
				$csvData = $obj->csvData;

				$data[ 'file' ] 		= (property_exists( $csvData, 'file' )) ? $csvData->file : '';
				$text_column  			= (property_exists( $csvData, 'text' )) ?
										  (
											(is_array($csvData->text)) ? $csvData->text : @intval($csvData->text)
										  ) : '';
				$value_column 			= (property_exists( $csvData, 'value' )) ? @intval($csvData->value) : '';
				$fields 				= (property_exists( $csvData, 'fields' ) && is_array($csvData->fields) ) ?
										  $csvData->fields : array();
				$data[ 'delimiter' ] 	= (property_exists( $csvData, 'delimiter' )) ? $csvData->delimiter : '';
				$data[ 'character' ] 	= (property_exists( $csvData, 'character' )) ? $csvData->character : ',';
				$data[ 'header' ] 		= (property_exists( $csvData, 'headline' )) ? $csvData->headline : '';
				$where 					= (property_exists( $csvData, 'where' )) ? $csvData->where : '';

				$vars = ( !empty( $data[ 'vars' ] )  && is_array( $data[ 'vars' ] ) ) ? $data[ 'vars' ] : array();
				$where = _cp_calculatedfieldsf_replace_variables( $where , $vars );
			}

			if( !empty( $data[ 'file' ] ))
			{
				$file = $data[ 'file' ];

				$delimiter = ',';
				if( !empty( $data[ 'delimiter' ] ) )
				{
					if( $data[ 'delimiter' ] == 'tabulator' ) $delimiter = "\t";
					elseif( !empty( $data[ 'character' ] ) ) $delimiter = $data[ 'character' ];
				}
				$includes_headers = ( !empty( $data[ 'header' ] ) && ( $data[ 'header' ] === 'true' || $data[ 'header' ] === 1 || $data[ 'header' ] === true)) ? true : false;

				add_filter('http_response', 'cp_calculatedfieldsf_ds_csv_correct_body' );
				$response = wp_remote_get( $file );
				remove_filter('http_response', 'cp_calculatedfieldsf_ds_csv_correct_body' );

				if( !is_wp_error( $response ) && $response['response']['code'] == 200 )
				{
					$body = wp_remote_retrieve_body($response);
					$body = preg_replace("/\r\n|\n\r|\n|\r/", "\n", $body);
					$rows = str_getcsv( $body, "\n" ); //parse the rows
					if( is_array( $rows ) && count( $rows ) )
					{
						$csv_arr = array();
						foreach( $rows as &$row )
						{
							$csv_arr[] = str_getcsv($row, $delimiter); //parse the items in rows
						}

						$headers = array();

						/*
							Get the headers row if exists, or a list of generic fields: Field_0, Field_1, ..., Field_#
						*/
						if( $includes_headers )
						{
							$headers = array_shift( $csv_arr );
						}
						else
						{
							$headers = array();
							for( $i = 0; $i < count( $csv_arr[0] ); $i++ )
							{
								$headers[] = 'Field_'.$i;
							}
						}

						if( $data[ 'return' ] == 'headers' )
						{
							// Return an array with the headers
							$return_obj->data = $headers;
						}
						else
						{
							if(
								isset( $text_column )  && $text_column  !== '' &&
								isset( $value_column ) && $value_column !== ''
							)
							{
								// Return an associative array
								// with all rows in the csv field that satisfy the "where" clause if correspond.
								$return_rows = array();
								if( is_array( $text_column ) )
								{
									foreach( $csv_arr as $index => $row )
									{
										$data = array();
										foreach( $text_column as $field )
										{
											$data[ $fields[ $field ] ] = ( isset( $row[ $field ] ) ) ? $row[ $field ]   : '';
										}

										// If was defined the "where" clause, check if the condition is satisfied
										if( isset( $where) && $where != '' )
										{
											if(
												isset( $data[ $fields[ $value_column ] ] ) &&
												$data[ $fields[ $value_column ] ] == $where
											) $return_rows[] = $data;
										}
										else
											$return_rows[] = $data;
									}
								}
								else
								{
									foreach( $csv_arr as $index => $row )
									{
										$data = array(
											'text' =>  ( isset( $row[ $text_column ] ) ) ? $row[ $text_column ]   : '',
											'value' => ( isset( $row[ $value_column ] ) ) ? $row[ $value_column ] : ''
										);

										// If was defined the "where" clause, check if the condition is satisfied
										if( isset( $where) && $where != '' )
										{
											if( $data[ 'value' ] == $where ) $return_rows[] = $data;
										}
										else
											$return_rows[] = $data;
									}
								}
								$return_obj->data = $return_rows;
							}
							else
							{
								$return_obj->error = __( 'Invalid columns' );
							}
						}
					}
					else
					{
						$return_obj->error = __( 'The file has not a CSV valid format' );
					}
				}
				else
				{
					$return_obj->error = __( 'The CSV file is not accessible' );
				}
			}
			else
			{
				$return_obj->error = __( 'The CSV file is not specified' );
			}
		}
		catch( Exception $err )
		{
			$return_obj->error = __( 'An error occurred processing the file' );
		}

		print ((version_compare(CP_CFF_PHPVERSION,"5.5.0")>=0)?json_encode( $return_obj, JSON_PARTIAL_OUTPUT_ON_ERROR ):json_encode( $return_obj ));
	}

function cp_calculatedfieldsf_ds_post_type( $data )
	{
		try
		{
			if( empty( $data[ 'form' ] ) && empty( $data[ 'field' ] ) ) return false;
			$obj = get_transient(  'cpcff_db_'.$data[ 'form' ].'_'.$data[ 'field' ] );
			if( $obj === false ) return false;

			$vars = ( !empty( $data[ 'vars' ] )  && is_array( $data[ 'vars' ] ) ) ? $data[ 'vars' ] : array();

			$data[ 'posttype' ] 	= $obj->posttypeData->posttype;
			$data[ 'value' ] 		= $obj->posttypeData->value;
			$data[ 'text' ] 		= $obj->posttypeData->text;
			$data[ 'last' ] 		= _cp_calculatedfieldsf_replace_variables( $obj->posttypeData->last, $vars );
			$data[ 'id' ] 			= _cp_calculatedfieldsf_replace_variables( $obj->posttypeData->id, $vars );

			$posts = array();
			if( $data[ 'id' ] === 0 || !empty( $data[ 'id' ] ) )
			{
				$result = get_post( $data[ 'id' ], ARRAY_A );
				if( !is_null( $result ) )
				{
					$tmp = new stdClass;
					_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
					_cp_calculatedfieldsf_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
					array_push( $posts, $tmp );
				}
			}
			else
			{
				$args = array(
					'post_status'  => 'publish',
					'orderby'        => 'post_date',
					'order'           => 'DESC'
				);

				if( !empty( $data[ 'posttype' ] ) )
				{
					$args[ 'post_type' ] = $data[ 'posttype' ];
				}

				if( $data[ 'last' ] === 0 )
				{
					return array();
				}
				if( !empty( $data[ 'last' ] ) )
				{
					$args[ 'numberposts' ] = intval( @$data[ 'last' ] );
				}

				$results = get_posts( $args );

				foreach ( $results as $result )
				{
					$tmp = new stdClass;
					_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
					_cp_calculatedfieldsf_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
					array_push( $posts, $tmp );
				}
			}
			return $posts;
		}
		catch( Exception $err )
		{
			return false;
		}
	}

function cp_calculatedfieldsf_ds_taxonomy( $data )
	{
		try
		{
			if( empty( $data[ 'form' ] ) && empty( $data[ 'field' ] ) ) return false;
			$obj = get_transient(  'cpcff_db_'.$data[ 'form' ].'_'.$data[ 'field' ] );
			if( $obj === false ) return false;

			$vars = ( !empty( $data[ 'vars' ] )  && is_array( $data[ 'vars' ] ) ) ? $data[ 'vars' ] : array();

			$data[ 'taxonomy' ] 	= $obj->taxonomyData->taxonomy;
			$data[ 'value' ] 			= $obj->taxonomyData->value;
			$data[ 'text' ] 			= $obj->taxonomyData->text;
			$data[ 'id' ] 				= _cp_calculatedfieldsf_replace_variables( $obj->taxonomyData->id, $vars );
			$data[ 'slug' ] 			= _cp_calculatedfieldsf_replace_variables( $obj->taxonomyData->slug, $vars );

			$taxonomies = array();
			if( $data[ 'id' ] === 0 || !empty( $data[ 'id' ] ) || $data[ 'slug' ] === 0 || !empty( $data[ 'slug' ] ) )
			{
				if( !empty( $data[ 'taxonomy' ] ) )
				{
					if( !empty( $data[ 'id' ] ) )
					{
						$result = get_term( $data[ 'id' ], $data[ 'taxonomy' ], ARRAY_A );
					}
					else
					{
						$result = get_term_by( 'slug', $data[ 'slug' ], $data[ 'taxonomy' ], ARRAY_A );
					}

					$tmp = new stdClass;
					$tmp->value = '';
					$tmp->text = '';
					if( !is_null( $result ) )
					{
						_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
						_cp_calculatedfieldsf_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
					}
					array_push( $taxonomies, $tmp );
				}
			}
			else
			{
				if( !empty( $data[ 'taxonomy' ] ) )
				{
					$results = get_terms( $data[ 'taxonomy' ], array( 'hide_empty' => 0 ) );

					foreach ( $results as $result )
					{
						$tmp = new stdClass;
						_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
						_cp_calculatedfieldsf_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
						array_push( $taxonomies, $tmp );
					}
				}
			}
			return $taxonomies;
		}
		catch( Exception $err )
		{
			return false;
		}
	}


function cp_calculatedfieldsf_ds_user( $data )
	{
		try
		{
			if( empty( $data[ 'form' ] ) && empty( $data[ 'field' ] ) ) return false;
			$obj = get_transient(  'cpcff_db_'.$data[ 'form' ].'_'.$data[ 'field' ] );
			if( $obj === false ) return false;

			$vars = ( !empty( $data[ 'vars' ] )  && is_array( $data[ 'vars' ] ) ) ? $data[ 'vars' ] : array();

			$data[ 'logged' ] 	= $obj->userData->logged;
			$data[ 'text' ] 		= $obj->userData->text;
			$data[ 'value' ] 		= $obj->userData->value;
			$data[ 'id' ] 			= _cp_calculatedfieldsf_replace_variables( $obj->userData->id, $vars );
			$data[ 'login' ] 		= _cp_calculatedfieldsf_replace_variables( $obj->userData->login, $vars );

			$users = array();
			if( !empty( $data[ 'logged' ] ) && $data[ 'logged' ] !== 'false' )
			{
				$result = wp_get_current_user();
				if( !empty( $result ) )
				{
					$tmp = new stdClass;
					_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
					$users[] = $tmp;
				}
			}
			elseif( $data[ 'id' ] === 0 || !empty( $data[ 'id' ] ) || $data[ 'login' ] === 0 || !empty( $data[ 'login' ] ) )
			{
				if( !empty( $data[ 'id' ] ) )
				{
					$result = get_user_by( 'id', $data[ 'id' ] );
				}
				elseif( !empty( $data[ 'login' ] ) )
				{
					$result = get_user_by( 'login', $data[ 'login' ] );
				}

				$tmp = new stdClass;
				$tmp->value = '';
				if( !empty( $result ) )
				{
					_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
				}
				$users[] = $tmp;

			}
			else
			{

				$results = get_users();
				foreach( $results as $result )
				{
					$tmp = new stdClass;
					_cp_calculatedfieldsf_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
					_cp_calculatedfieldsf_set_attr( $tmp, 'text', $result->data, $data[ 'text' ] );
					$users[] = $tmp;
				}
			}

			return $users;
		}
		catch( Exception $err )
		{
			return false;
		}
	}

// AUXILIARY FUNCTIONS

if (!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') {
        if (is_string($input) && !empty($input)) {
            $output = array();
            $tmp    = preg_split("/".$eol."/",$input);
            if (is_array($tmp) && !empty($tmp)) {
                while (list($line_num, $line) = each($tmp)) {
                    if (preg_match("/".$escape.$enclosure."/",$line)) {
                        while ($strlen = strlen($line)) {
                            $pos_delimiter       = strpos($line,$delimiter);
                            $pos_enclosure_start = strpos($line,$enclosure);
                            if (
                                is_int($pos_delimiter) && is_int($pos_enclosure_start)
                                && ($pos_enclosure_start < $pos_delimiter)
                                ) {
                                $enclosed_str = substr($line,1);
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure);
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end);
                                $output[$line_num][] = $enclosed_str;
                                $offset = $pos_enclosure_end+3;
                            } else {
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                                    $output[$line_num][] = substr($line,0);
                                    $offset = strlen($line);
                                } else {
                                    $output[$line_num][] = substr($line,0,$pos_delimiter);
                                    $offset = (
                                                !empty($pos_enclosure_start)
                                                && ($pos_enclosure_start < $pos_delimiter)
                                                )
                                                ?$pos_enclosure_start
                                                :$pos_delimiter+1;
                                }
                            }
                            $line = substr($line,$offset);
                        }
                    } else {
                        $line = preg_split("/".$delimiter."/",$line);

                        /*
                         * Validating against pesky extra line breaks creating false rows.
                         */
                        if (is_array($line) && !empty($line[0])) {
                            $output[$line_num] = $line;
                        }
                    }
                }
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

?>