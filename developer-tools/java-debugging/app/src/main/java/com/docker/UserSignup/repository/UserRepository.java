package com.docker.UserSignup.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import com.docker.UserSignup.model.User;

@Repository("userRepository")
public interface UserRepository extends JpaRepository<User, Long> {
	
	@Query("select s from User s where s.userName = :userName")
	User findByUserName(@Param("userName") String userName);
	
}
