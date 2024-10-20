import React, { useState, useEffect } from "react";

// import { useNavigate } from "react-router-dom";

import AuthService from "../services/AuthService";

import { useNavigate } from "react-router-dom";

// import UserService from "../services/user.service";

// import AuthVerify from "../common/AuthVerify";

import { getJwt } from "../common/AuthVerify";

const Login = () => {
  const navigate = useNavigate();

  useEffect(()=>{
    const jwt = getJwt();
    if( jwt ){
      navigate("/home");
    }
  });


  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [c_password, setC_Password] = useState("");
  const [message, setMessage] = useState("");
  const [messageRegister, setMessageRegister] = useState("");

  // const navigate = useNavigate();
  
  const onChangeEmail = (e) => {
    const email = e.target.value;
    setEmail(email);
  };

  const onChangePassword = (e) => {
    const password = e.target.value;
    setPassword(password);
  };

  const onChangeC_Password = (e) => {
    const password = e.target.value;
    setC_Password(password);
  };

  const handleLogin = (e) => {
    e.preventDefault();

    setMessage("");
    localStorage.removeItem('jwt-token');

    AuthService.login(email, password)
    .then(res=>{
      if(res.data.success){
        localStorage.setItem('jwt-token', res.data.token);

        navigate("/home");
      }else if( res.data.error ){
        setMessage(res.data.error.message);
      }else{
        console.log(res);
      }
    })
    .catch(err=>{
      setMessage(err.response.data.error.errors[0].msg);
    });
  };
  

  const handleRegister = (e) => {
    e.preventDefault();

    setMessageRegister("");

    AuthService.register(email, password, c_password)
    .then(res=>{
      if(res.data.success){
        setMessageRegister("Success Register");
      }else if( res.data.error ){
        setMessageRegister(res.data.error.message);
      }else{
        console.log(res);
      }
    })
    .catch(err=>{
      setMessageRegister(err.response.data.error.errors[0].msg);
    });
  };


  return (
    <div className="container">
      <div className="">
        <label>Login</label>
        <form onSubmit={handleLogin}>
          <input onChange={onChangeEmail} type='text' placeholder='Email' />
          <input onChange={onChangePassword} type='password' placeholder='Password' />
          <input type='submit' value="Login" />
          <label>{message}</label>
          <a href="#">Register?</a>
        </form>
      </div>
      <div className="">
        <label>Register</label>
        <form onSubmit={handleRegister}>
          <input onChange={onChangeEmail} type='text' placeholder='Email' />
          <input onChange={onChangePassword} type='password' placeholder='Password' />
          <input onChange={onChangeC_Password} type='password' placeholder='Confirm Password' />
          <input type='submit' value="Register" />
          <label>{messageRegister}</label>
          <a href="#">Already have an accout, goto login?</a>
        </form>
      </div>
    </div>
  );
};

export default Login;