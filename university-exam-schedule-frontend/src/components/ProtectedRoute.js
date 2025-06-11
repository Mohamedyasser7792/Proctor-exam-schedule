import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';

// Mock authentication function (Replace this with real authentication logic)
const isAuthenticated = () => {
  return localStorage.getItem('authToken') !== null;
};

const ProtectedRoute = () => {
  return isAuthenticated() ? <Outlet /> : <Navigate to="/login" />;
};

export default ProtectedRoute;
