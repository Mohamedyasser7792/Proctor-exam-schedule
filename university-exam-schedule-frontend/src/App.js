import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Home from './components/Home';
import Login from './components/Login'; // Fixed import (was lowercase)

// Pages
import TeachingAssistants from './pages/TeachingAssistants';
import StudyGroups from './pages/StudyGroups';
import ExamHalls from './pages/ExamHalls';
import StudySubjects from './pages/StudySubjects';
import ExamSchedule from './pages/ExamSchedule';
import CreateExamSchedule from './pages/CreateExamSchedule';
import ExamScheduleTeachingAssistants from './pages/ExamScheduleTeachingAssistants'; // New import for the Teaching Assistants page

// Protected Route Component
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <Router>
      <Routes>
        {/* Public Routes */}
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />

        {/* Protected Routes */}
        <Route element={<ProtectedRoute />}>
          <Route element={<Layout />}>
            <Route path="teaching-assistants" element={<TeachingAssistants />} />
            <Route path="study-groups" element={<StudyGroups />} />
            <Route path="exam-halls" element={<ExamHalls />} />
            <Route path="study-subjects" element={<StudySubjects />} />
            <Route path="exam-schedule" element={<ExamSchedule />} />
            <Route path="create-exam-schedule" element={<CreateExamSchedule />} />
            <Route path="exam-schedule-teaching-assistants" element={<ExamScheduleTeachingAssistants />} />
          </Route>
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
