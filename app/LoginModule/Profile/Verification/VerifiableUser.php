<?php

namespace App\LoginModule\Profile\Verification;

trait VerifiableUser {

    public function getPrimaryEmailVerifiedAttribute() {
        $primary = $this->emails()->primary()->first();
        return $primary ? $primary->verified : false;
    }


    public function getSecondaryEmailVerifiedAttribute() {
        $secondary = $this->emails()->secondary()->first();
        return $secondary ? $secondary->verified : false;
    }


    public function getTeacherDomainVerifiedAttribute() {
        if($this->role === 'teacher') {
            return TeacherDomain::verify($this);
        }
        return true;
    }

}