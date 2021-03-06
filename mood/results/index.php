<?php

    require_once('../../apiEndpoint.php');

    class GetMoodResults extends ApiEndpoint {

        protected function getMethod() {
            return "GET"; 
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $db->real_escape_string($_GET['id']);
            $filter = $_GET['filter'];

            $json = array();
            $selection = array_unique(explode("|", $filter));

            if (sizeOf($selection) === 0 || !isset($filter)) {
                $selection = array("own", "student", "teacher");
            }

            foreach ($selection as $cur) {
                switch($cur) {
                    case "own":
                        parent::exitOnBadRequest($uid);
                        $queryId = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Vote.uid = $uid GROUP BY vdate ORDER BY vdate DESC";
                        $arrayOwn = $this->getArray($db->query($queryId));
                        $json["own"] = $arrayOwn;
                        break;
                    case "student":
                        $queryStudent = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Users.uid = Vote.uid AND Users.upermission != 2 GROUP BY vdate ORDER BY vdate DESC";
                        $arrayStudent = $this->getArray($db->query($queryStudent));
                        $json["student"] = $arrayStudent;
                        break;
                    case "teacher":
                        $queryTeacher = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Users.uid = Vote.uid AND Users.upermission = 2 GROUP BY vdate ORDER BY vdate DESC";
                        $arrayTeacher = $this->getArray($db->query($queryTeacher));
                        $json["teacher"] = $arrayTeacher;
                        break;
                }
            }

            parent::returnApiResponse($json);

            $db->close();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::ONLY_AUTHENTICATION;
        }

        private function getArray($result) {
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $array = array();
            while($row = $result->fetch_assoc()) {
                $arrayEntry = array(
                    "value" => $row['vvalue'],
                    "date" => $row['vday'].".".$row['vmonth'].".".$row['vyear']
                );
                $array[] = $arrayEntry;
            }

            return $array;
        }

    }

    new GetMoodResults();

?>