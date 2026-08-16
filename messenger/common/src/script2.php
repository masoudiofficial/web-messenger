<?php

namespace root\src\script2;

if (isset($_SESSION['user-username']) && xauthentication()) {

    class Functions {

        public function xaccountimage($dir) {

            $filename = 'accountimage';
            $extensions = ['gif', 'jpeg', 'jpg', 'png', 'webp'];
            foreach ($extensions as $ext) {
                $fullPath = "$dir/$filename.$ext";
                if (file_exists($fullPath)) {
                    return $fullPath;
                }
            }
        }

        # ...
    }

}
?>
