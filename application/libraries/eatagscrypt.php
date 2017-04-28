<?php
class eatagscrypt {
    public function fnEncrypt($sValue, $sSecretKey)
    {
        if (!isset($sValue)) return NULL;
        if (is_null($sValue)) return NULL;
        if (strlen($sValue) == 0) return NULL;

        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $sSecretKey, $sValue,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256,
                            MCRYPT_MODE_ECB
                        ),
                        MCRYPT_RAND)
                    )
                ), "\0"
            );
    }

    public function fnDecrypt($sValue, $sSecretKey)
    {
        if (!isset($sValue)) return NULL;
        if (is_null($sValue)) return NULL;
        if (strlen($sValue) == 0) return NULL;

        return rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $sSecretKey,
                base64_decode($sValue),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
            ), "\0"
        );
    }
}
?>