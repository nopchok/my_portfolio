import React from "react";
// import { withRouter } from "react-router-dom";

import { useNavigate } from "react-router-dom";
import { useEffect } from "react";
import { jwtDecode } from "jwt-decode";

export const getJwt = () => {
  const token = localStorage.getItem('jwt-token');
  let auth = null;
  try{
    auth = jwtDecode(token);
    if( new Date(auth.exp*1000) < new Date() ){
      auth = null;
      localStorage.removeItem('jwt-token');
    }
  }catch(e){
  }
  return auth;
};

const AuthVerify = () => {
  const navigate = useNavigate();

  useEffect(()=>{
    const jwt = getJwt();
    
    if( !jwt ){
      navigate("/");
    }else{

    }
  });

  return <div></div>;
};

export default AuthVerify;