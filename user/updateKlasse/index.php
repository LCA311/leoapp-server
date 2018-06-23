<?php

    require_once('../../apiEndpoint.php');

    new UpdateKlasse();

    class UpdateKlasse extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {

            $db = getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $uklasse = $db->real_escape_string($_POST['uklasse']);

            exitOnBadRequest($uid, $uklasse);

            $query = "UPDATE Users SET uklasse = '$uklasse' WHERE uid = $uid";
            $result = $db->query($query);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>