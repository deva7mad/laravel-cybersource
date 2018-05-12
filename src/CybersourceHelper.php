<?php namespace JustGeeky\LaravelCybersource;

class CybersourceHelper {

    public static function sign(array $params, $secretKey) {
        return static::signData(static::buildDataToSign($params), $secretKey);
    }
    
    public static function signData($data, $secretKey) {
        return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
    }

    public static function buildDataToSign($params) {
        $signedFieldNames = static::csvToArray($params["signed_field_names"]);
        $signMe = '';
        foreach ($signedFieldNames as $field) {
            $signMe .= $field . '=' . $params[$field] . ',';
        }

        return rtrim($signMe, ',');
    }

    public static function csvToArray($csv) {
        return explode(",", $csv);
    }

    public static function arrayToCsv(array $array) {
        return implode(',', $array);
    }

    public static function str_getcsv ( $input, $delimiter = ',', $enclosure = '"', $escape = '\\' ) {

        // open a temporary "file" that's actually just in memory
        $t = fopen( 'php://memory', 'rw' );

        // write the contents of our CSV to it
        fwrite( $t, $input );

        // skip back to the beginning of the file
        fseek( $t, 0 );

        // get the first row, they're the headers
        $headers = fgetcsv( $t, null, $delimiter, $enclosure, $escape );

        $rows = array();
        while( !feof( $t ) && $row = fgetcsv( $t, null, $delimiter, $enclosure, $escape )) {

            if(count($headers) != count($row)) {
                $diff = abs(count($headers) - count($row));
                $row = array_slice($row, 0, count($row) - $diff);
            }
            $row = array_combine( $headers, $row );

            $rows[] = $row;
        }

        fclose( $t );

        return $rows;

    }

} 