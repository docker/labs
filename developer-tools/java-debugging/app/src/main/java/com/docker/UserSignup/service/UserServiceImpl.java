package com.docker.UserSignup.service;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.docker.UserSignup.model.User;
import com.docker.UserSignup.repository.UserRepository;

@Service("userService")
public class UserServiceImpl implements UserService {

	@Autowired
	private UserRepository userRepository;
	
	@Transactional
	public User save(User user) {
		return userRepository.save(user);
	}

	public boolean findByLogin(String userName, String password) {	
		User usr = userRepository.findByUserName(userName);

		if(usr != null && usr.getPassword().equals(password)) {
			return true;
		} 
		
		return false;		
	}

	public boolean findByUserName(String userName) {
		User usr = userRepository.findByUserName(userName);
		
		if(usr != null) {
			return true;
		}
		
		return false;
	}

}
