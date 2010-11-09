<?php
/**
 * WordPress Query class.
 *
 * Abstract class for handling advanced queries
 *
 * @package WordPress
 * @since 3.1.0
 */
class WP_Object_Query {

	/**
	 * Query vars, after parsing
	 *
	 * @since 3.1.0
	 * @access public
	 * @var array
	 */
	var $query_vars;

	/**
	 * Retrieve query variable.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @return mixed
	 */
	function get( $query_var ) {
		if ( isset( $this->query_vars[$query_var] ) )
			return $this->query_vars[$query_var];

		return '';
	}

	/**
	 * Set query variable.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed $value Query variable value.
	 */
	function set( $query_var, $value ) {
		$this->query_vars[ $query_var ] = $value;
	}

	/*
	 * Populates the $meta_query property
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $qv The query variables
	 */
	function parse_meta_query( &$qv ) {
		$meta_query = array();

		// Simple query needs to be first for orderby=meta_value to work correctly
		foreach ( array( 'key', 'value', 'compare', 'type' ) as $key ) {
			if ( !empty( $qv[ "meta_$key" ] ) )
				$meta_query[0][ $key ] = $qv[ "meta_$key" ];
		}

		if ( !empty( $qv['meta_query'] ) && is_array( $qv['meta_query'] ) ) {
			$meta_query = array_merge( $meta_query, $qv['meta_query'] );
		}

		$qv['meta_query'] = $meta_query;
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple taxonomies
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $tax_query List of taxonomy queries. A single taxonomy query is an associative array:
	 * - 'taxonomy' string|array The taxonomy being queried
	 * - 'terms' string|array The list of terms
	 * - 'field' string (optional) Which term field is being used.
	 *		Possible values: 'term_id', 'slug' or 'name'
	 *		Default: 'slug'
	 * - 'operator' string (optional)
	 *		Possible values: 'IN' and 'NOT IN'.
	 *		Default: 'IN'
	 * - 'include_children' bool (optional) Whether to include child terms.
	 *		Default: true
	 *
	 * @param string $object_id_column
	 * @return string
	 */
	function get_tax_sql( $tax_query, $object_id_column ) {
		global $wpdb;

		$sql = array();
		foreach ( $tax_query as $query ) {
			if ( !isset( $query['include_children'] ) )
				$query['include_children'] = true;

			$query['do_query'] = false;

			$sql_single = get_objects_in_term( $query['terms'], $query['taxonomy'], $query );

			if ( empty( $sql_single ) )
				return ' AND 0 = 1';

			$sql[] = $sql_single;
		}

		if ( 1 == count( $sql ) ) {
			$ids = $wpdb->get_col( $sql[0] );
		} else {
			$r = "SELECT object_id FROM $wpdb->term_relationships WHERE 1=1";
			foreach ( $sql as $query )
				$r .= " AND object_id IN ($query)";

			$ids = $wpdb->get_col( $r );
		}

		if ( !empty( $ids ) )
			return " AND $object_id_column IN(" . implode( ', ', $ids ) . ")";
		else
			return ' AND 0 = 1';
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param string $string
	 * @param array $cols
	 * @param bool $wild Whether to allow trailing wildcard searches. Default is false.
	 * @return string
	 */
	function get_search_sql( $string, $cols, $wild = false ) {
		$string = esc_sql( $string );

		$searches = array();
		$wild_char = ( $wild ) ? '%' : '';
		foreach ( $cols as $col ) {
			if ( 'ID' == $col )
				$searches[] = "$col = '$string'";
			else
				$searches[] = "$col LIKE '$string$wild_char'";
		}

		return ' AND (' . implode(' OR ', $searches) . ')';
	}
}

?>
