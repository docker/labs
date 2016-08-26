package com.docker.UserSignup.service;

import com.docker.UserSignup.model.User;

public interface UserService {
	User save(User user);
	boolean findByLogin(String userName, String password);
	boolean findByUserName(String userName);
}
