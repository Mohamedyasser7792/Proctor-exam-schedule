import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { loginUser } from '../services/api';
import './Login.css';

// import logo from '../assets/logo_login.png';


function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    try {
      const response = await loginUser({ username: email, password });
      localStorage.setItem('authToken', response.data.token);
      navigate('/teaching-assistants');
    } catch (error) {
      alert('Invalid credentials');
    }
  };

  return (
    <div className="login-page">
      <div className="login-background-blur"></div>
      <div className="login-background-blur-bottom"></div>

      <header className="login-header">
        {/* <div className="login-logo">
          <img src={logo} alt="Logo" />
        </div> */}
        <h1 className="login-title">Hi, welcome back!</h1>
        <p>
          First time here? <span className="login-text-white">Sign up for free</span>
        </p>
      </header>

      <form className="login-form" onSubmit={handleLogin}>
        <input
          // type="email"
          name="email"
          placeholder="Your email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />

        <input
          type="password"
          name="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          minLength="6"
          required
        />

        {/* <p>Password must be at least 6 characters</p> */}

        <button type="submit">login</button>

        {/* <p>
          You acknowledge that you read, and agree, to our{' '}
          <a href="#">Terms of Service</a> and our <a href="#">Privacy Policy</a>.
        </p> */}
      </form>
    </div>
  );
}

export default Login;








































// import React, { useState } from 'react';
// import { useNavigate } from 'react-router-dom';
// import { loginUser } from '../services/api';
// import './Login.css';

// function Login() {
//   const [username, setUsername] = useState('');
//   const [password, setPassword] = useState('');
//   const navigate = useNavigate();

//   const handleLogin = async (e) => {
//     e.preventDefault();
//     try {
//       const response = await loginUser({ username, password });
//       localStorage.setItem('authToken', response.data.token);
//       navigate('/teaching-assistants');
//     } catch (error) {
//       alert('Invalid credentials');
//     }
//   };

//   return (
// <div className="login-background">
//   <div className="login-container">
//     <div className="login-card">
//       <div className="login-card2">
//         <form className="login-form" onSubmit={handleLogin}>
//           <p className="login-heading">AlgoSched</p>
//           <div className="login-field">
//             <svg
//               viewBox="0 0 16 16"
//               fill="currentColor"
//               height="16"
//               width="16"
//               xmlns="http://www.w3.org/2000/svg"
//               className="login-input-icon"
//             >
//               <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
//             </svg>
//             <input
//               type="text"
//               className="login-input-field"
//               placeholder="Username"
//               value={username}
//               onChange={(e) => setUsername(e.target.value)}
//               autoComplete="off"
//             />
//           </div>
//           <div className="login-field">
//             <svg
//               viewBox="0 0 16 16"
//               fill="currentColor"
//               height="16"
//               width="16"
//               xmlns="http://www.w3.org/2000/svg"
//               className="login-input-icon"
//             >
//               <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
//             </svg>
//             <input
//               type="password"
//               className="login-input-field"
//               placeholder="Password"
//               value={password}
//               onChange={(e) => setPassword(e.target.value)}
//             />
//           </div>
//           <div className="login-btn">
//             <button type="submit" className="login-button1">
//               Login
//             </button>
//           </div>
//         </form>
//       </div>
//     </div>
//   </div>
// </div>

//   );
// }

// export default Login;
