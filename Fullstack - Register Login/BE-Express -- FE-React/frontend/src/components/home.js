import React, { useState, useEffect } from "react";

import { useNavigate } from "react-router-dom";

import AuthService from "../services/AuthService";
// import UserService from "../services/user.service";

import AuthVerify from "../common/AuthVerify";

const Home = () => {
  // const navigate = useNavigate();
  
  const [data, setData] = useState("");

  const onClickLogout = ()=>{
    localStorage.removeItem('jwt-token');
    
    // navigate("/");
    window.location.reload();
  };

  const getData = ()=>{
    AuthService.getData('get', "/test", {})
    .then(res=>{
      setData(res.data);
    })
  }
  
  
  return (
    <div className="container">
      <AuthVerify />
      <div>Hello</div>
      <div>
        <a href="" onClick={onClickLogout}>Logout</a>
      </div>
      <button onClick={getData}>Get data with auth</button>
      <div>{data}</div>
    </div>
  );
};

export default Home;