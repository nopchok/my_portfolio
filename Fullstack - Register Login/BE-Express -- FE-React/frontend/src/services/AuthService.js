import axios from "axios";


const api = axios.create({
  baseURL: "http://localhost:8080/api/v1",
});


api.interceptors.request.use(config => {
  const accessToken = localStorage.getItem('jwt-token');

  if( accessToken ){
    config.headers.Authorization = `Bearer ${accessToken}`;
  }
  return config;
});

const getData = (method, path, params)=>{
  if( method.toLowerCase() === 'post' ){
    return api.post(path, {...params});
  }else{
    return api.get(path, {...params});
  }
}

const register = (email, password, c_password) => {
  return api.post("/user/register", {
    email,
    password,
    c_password,
  });
};

const login = (email, password) => {
  return new Promise((resolve, reject)=>{
    api.post("/user/login", {
      email,
      password,
    })
    .then((response) => {
      resolve( response );
    })
    .catch((err)=>{
      reject( err );
    });
  });
};

const logout = () => {
  localStorage.removeItem("user");
  return api.post("/user/logout").then((response) => {
    return response.data;
  });
};

const getCurrentUser = () => {
  return JSON.parse(localStorage.getItem("user"));
};

const AuthService = {
  register,
  login,
  logout,
  getCurrentUser,
  getData,
}

export default AuthService;