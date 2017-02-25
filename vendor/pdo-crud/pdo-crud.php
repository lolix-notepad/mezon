<?php

    /**
    *   Class provides simple CRUD operations.
    */
    class           PdoCrud
    {
        /**
        *   PDO object.
        */
        var             $PDO = false;

        /**
        *   Method connects to the database.
        */
        function            connect( $ConnnectionData )
        {
            // no need to test this single string. assume that PDO developers did it
            $this->PDO = new PDO( 
                $ConnnectionData[ 'dsn' ] , $ConnnectionData[ 'user' ] , $ConnnectionData[ 'password' ]
            );
        }

        /**
        *   Method builds select query.
        */
        protected function  select_query( $Fields , $TableNames , $Where , $From , $Limit )
        {
            $Query = 'SELECT '.htmlspecialchars( $Fields ).' FROM '.htmlspecialchars( $TableNames ).' WHERE '.
                htmlspecialchars( $Where ).' LIMIT '.intval( $From ).' , '.intval( $Limit );

            return( $Query );
        }

        /**
        *   Getting records.
        */
        function            select( $Fields , $TableNames , $Where = '1 = 1' , $From = 0 , $Limit = 1000000 )
        {
			var_dump( $this->select_query( $Fields , $TableNames , $Where , $From , $Limit ) );
            $Result = $this->PDO->query( $this->select_query( $Fields , $TableNames , $Where , $From , $Limit ) );

            return( $Result->fetchAll() );
        }
    }

?>