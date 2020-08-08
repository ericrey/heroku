<?php
        public function updateUser($email, $name, $school, $id){
            $stmt = $this->con->prepare("UPDATE users SET email = ?, name = ?, school = ? WHERE id = ?");
            $stmt->bind_param("sssi", $email, $name, $school, $id);
            if($stmt->execute())
                return true; 
            return false; 
        }

        public function updatePassword($currentpassword, $newpassword, $email){
            $hashed_password = $this->getUsersPasswordByEmail($email);
            
            if(password_verify($currentpassword, $hashed_password)){
                
                $hash_password = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $this->con->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss",$hash_password, $email);

                if($stmt->execute())
                    return PASSWORD_CHANGED;
                return PASSWORD_NOT_CHANGED;

            }else{
                return PASSWORD_DO_NOT_MATCH; 
            }
        }

        public function deleteUser($id){
            $stmt = $this->con->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute())
                return true; 
            return false; 
        }
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT UserID, Name, Email, Password, Balance FROM users;");
            $stmt->execute(); 
            $stmt->bind_result($StudentID, $Name, $Email, $Password, $Balance);
            $users = array(); 
            while($stmt->fetch()){ 
                $user = array(); 
                $user['UserID'] = $StudentID; 
                $user['Name']=$Name;
                $user['Email']=$Email; 
                $user['Password'] = $Password; 
                $user['Balance'] = $Balance; 
                array_push($users, $user);
            }             
            return $users; 
        }