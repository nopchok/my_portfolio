// import logo from './logo.svg';
import './App.css';

import Home from './components/home';
import Login from './components/login';

import { BrowserRouter, Routes, Route, Navigate, Link } from "react-router-dom";

function Appsss() {
  return (
    <div className="">
      <div className="">
        <label>Login</label>
        <form method="POST" action="http://localhost:8080/api/v1/user/login">
          <input type='text' name="email" placeholder='Email' />
          <input type='password' name="password" placeholder='Password' />
          <input type='submit' value="Login" />
          <a href="#">Register?</a>
        </form>
      </div>
      <div className="">
        <label>Register</label>
        <form method="POST" action="http://localhost:8080/api/v1/user/register">
          <input type='text' name="email" placeholder='Email' />
          <input type='password' name="password" placeholder='Password' />
          <input type='password' name="c_password" placeholder='Confirm Password' />
          <input type='submit' value="Register" />
          <a href="#">Already have an accout, goto login?</a>
        </form>
      </div>
    </div>
  );
}



function App() {
  const NoMatch = () => {
    return (
      <h3>404</h3>
    );
  };

  return (
    <div className="">
      <BrowserRouter>
        <Routes>
          {/* <Route path="/" element={!auth.email ? <Login /> : <Navigate to="/home" replace />} />
          <Route path="/home" element={auth.email ? <Home /> : <Navigate to="/" replace />} /> */}
          <Route path="/" element={<Login />} />
          <Route path="/home" element={<Home />} />
          <Route path="*" element={<NoMatch />}/>
        </Routes>
      </BrowserRouter>
    </div>
  );
}



export default App;
