import React from 'react';
import { Link, Outlet, useNavigate } from 'react-router-dom';
import './Layout.css';

const Layout = () => {
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('authToken'); // Clear auth token
    navigate('/'); // Redirect to the login page
  };

  return (
    <div className="layout">
      {/* Header Section */}
      <header className="layout-header">
        <nav>
          <ul className="nav-links">
            <li><Link to="/teaching-assistants">Enter Teaching Assistants</Link></li>
            <li><Link to="/study-groups">Enter Study Groups</Link></li>
            <li><Link to="/exam-halls">Enter Exam Halls</Link></li>
            <li><Link to="/study-subjects">Enter Study Subjects</Link></li>
            <li><Link to="/exam-schedule">Enter Exam Schedule Data</Link></li>
            <li><Link to="/create-exam-schedule">Create Exam Schedule</Link></li>
            <li><button className="logout-button" onClick={handleLogout}>Logout</button></li>
          </ul>
        </nav>
      </header>

      {/* Main Content Section */}
      <main className="layout-content">
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;
