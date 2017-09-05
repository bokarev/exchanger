<?php
if(!class_exists('WMInterfaces')){
class WMInterfaces {
    public static function getPassportInfo( $iWMID ) {
        $aResponse = self::request( 'https://passport.webmoney.ru/xml/XMLGetWMIDInfo.aspx', '<request><wmid>'.$iWMID.'</wmid></request>' );
        if( !isset( $aResponse['@attributes'] ) || !isset( $aResponse['@attributes']['retval'] ) )
            throw new Exception( var_export( $aResponse, true ) );
        if( $aResponse['@attributes']['retval'] != 0 )
            throw new Exception( 'Retval '.$aResponse['@attributes']['retval'] );
        return array(
            'iAttestat' => $aResponse['certinfo']['attestat']['row']['@attributes']['tid'],
            'sAttestat' => $aResponse['certinfo']['attestat']['row']['@attributes']['typename'],
            'sDateReg' => date( 'd.m.Y', strtotime( str_replace( '.', '/', $aResponse['certinfo']['wmids']['row']['@attributes']['datereg'] ) ) ),
            'iLevel' => $aResponse['certinfo']['wmids']['row']['@attributes']['level'],
            'iPosClaims' => $aResponse['certinfo']['claims']['row']['@attributes']['posclaimscount'],
            'iNegClaims' => $aResponse['certinfo']['claims']['row']['@attributes']['negclaimscount'],
            'sClaimsDate' => date( 'd.m.Y', strtotime( str_replace( '.', '/', $aResponse['certinfo']['claims']['row']['@attributes']['claimslastdate'] ) ) )
        );
        
    }
    private static function request( $sURL, $sXML ) {
        $oCURL = curl_init( $sURL );
        curl_setopt_array( $oCURL, array( 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $sXML
        ) );
        if( ($sResponse = curl_exec( $oCURL )) === false || curl_errno( $oCURL ) )
            throw new Exception( curl_error( $oCURL ), curl_errno( $oCURL ) );
        curl_close( $oCURL );
        return json_decode( json_encode( simplexml_load_string( $sResponse ) ), true );
    }
}
}
?>
