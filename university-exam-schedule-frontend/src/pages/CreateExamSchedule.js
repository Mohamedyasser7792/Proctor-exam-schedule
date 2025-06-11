import React, { useState, useEffect } from 'react';
import { getLatestExamSchedules, generateExamSchedule, getSchedulingErrors, clearData ,exportLastExamSchedule } from '../services/api';
import './CreateExamSchedule.css';
import { FaRedo, FaExclamationTriangle, FaSyncAlt, FaUserFriends, FaCalendarAlt, FaCheckCircle, FaSpinner, FaClock, FaUsers, FaChalkboardTeacher, FaFileExcel, FaTrashAlt, FaEye } from 'react-icons/fa';
import { useNavigate } from 'react-router-dom';

import Excel from '../assets/Excel.png'

function CreateExamSchedule() {
  const [examSchedules, setExamSchedules] = useState([]);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState([]);
  const [showErrors, setShowErrors] = useState(false);
  const [showConfirmation, setShowConfirmation] = useState(false);
  const navigate = useNavigate();
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);
  const [schedulingErrors, setSchedulingErrors] = useState([]);
  const [generationStats, setGenerationStats] = useState(null);

  useEffect(() => {
    fetchExamSchedules();
  }, []);

  const fetchExamSchedules = async () => {
    try {
      setLoading(true);
      const response = await getLatestExamSchedules();
      setExamSchedules(response.data.data || []);
    } catch (error) {
      console.error('Error fetching exam schedules:', error);
      setExamSchedules([]);
    } finally {
      setLoading(false);
    }
  };

  const handleGenerate = async () => {
    setLoading(true);
    setError(null);
    setSuccess(false);
    setSchedulingErrors([]);
    setGenerationStats(null);

    try {
      const response = await generateExamSchedule();
      setSuccess(true);
      setGenerationStats(response.data.statistics);

      // Fetch scheduling errors if any
      const errorsResponse = await getSchedulingErrors();
      if (errorsResponse.data.length > 0) {
        setSchedulingErrors(errorsResponse.data);
      }
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to generate exam schedule');
    } finally {
      setLoading(false);
    }
  };

  const handleShowErrors = async () => {
    try {
      setLoading(true);
      const response = await getSchedulingErrors();
      setErrors(response.data || []);
      setShowErrors(true);
    } catch (error) {
      console.error('Error fetching scheduling errors:', error);
      setErrors([]);
    } finally {
      setLoading(false);
    }
  };

  const handleRecreateTable = () => {
    setShowConfirmation(true);
  };

  const confirmRecreateTable = async () => {
    try {
      setLoading(true);
      const response = await clearData();
      await fetchExamSchedules();
      alert(response.data.message || 'Data cleared and table recreated successfully.');
    } catch (error) {
      console.error('Error clearing data:', error);
      alert(error.response?.data?.message || 'Failed to clear data. Please try again.');
    } finally {
      setShowConfirmation(false);
      setLoading(false);
    }
  };

  const closeModal = () => {
    setShowErrors(false);
    setErrors([]);
  };

  const cancelRecreateTable = () => {
    setShowConfirmation(false);
  };

  const handleNavigateToTAs = () => {
    navigate('/exam-schedule-teaching-assistants');
  };

  return (
    <div className="create-exam-schedule-container">
      <div className="header-section">
        <h2><FaCalendarAlt className="header-icon" /> Create Exam Schedule</h2>
        <p className="description">
          Generate a new exam schedule based on current teaching assistant assignments and hall availability.
        </p>
      </div>

      <div className="generate-section">
        <button 
          onClick={handleGenerate}
          className={`generate-btn ${loading ? 'loading' : ''}`}
          disabled={loading}
        >
          {loading ? (
            <>
              <FaSpinner className="spinner" />
              Generating Schedule...
            </>
          ) : (
            <>
              <FaCalendarAlt />
              Generate New Schedule
            </>
          )}
        </button>
      </div>

      {error && (
        <div className="error-message">
          <FaExclamationTriangle className="error-icon" />
          <span>{error}</span>
        </div>
      )}

      {success && (
        <div className="success-message">
          <FaCheckCircle className="success-icon" />
          <span>Schedule generated successfully!</span>
        </div>
      )}

      {generationStats && (
        <div className="stats-section">
          <h3>Generation Statistics</h3>
          <div className="stats-grid">
            <div className="stat-card">
              <FaClock className="stat-icon" />
              <div className="stat-content">
                <span className="stat-value">{generationStats.total_exams}</span>
                <span className="stat-label">Total Exams</span>
              </div>
            </div>
            <div className="stat-card">
              <FaUsers className="stat-icon" />
              <div className="stat-content">
                <span className="stat-value">{generationStats.assigned_tas}</span>
                <span className="stat-label">Assigned TAs</span>
              </div>
            </div>
            <div className="stat-card">
              <FaExclamationTriangle className="stat-icon" />
              <div className="stat-content">
                <span className="stat-value">{generationStats.conflicts}</span>
                <span className="stat-label">Conflicts</span>
              </div>
            </div>
            <div className="stat-card">
              <FaChalkboardTeacher className="stat-icon" />
              <div className="stat-content">
                <span className="stat-value">{generationStats.errors}</span>
                <span className="stat-label">Errors</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {schedulingErrors.length > 0 && (
        <div className="scheduling-errors">
          <h3>
            <FaExclamationTriangle className="error-icon" />
            Scheduling Errors
          </h3>
          <div className="errors-list">
            {schedulingErrors.map((error, index) => (
              <div key={index} className="error-item">
                <div className="error-header">
                  <span className="error-group">
                    {error.group_name || 'General Error'}
                  </span>
                  {error.subject_name && (
                    <span className="error-subject">
                      - {error.subject_name}
                    </span>
                  )}
                </div>
                <p className="error-message">{error.error_message}</p>
              </div>
            ))}
          </div>
        </div>
      )}

      <div className="buttons-container">
        <div className="button-group primary-actions">
          <button className="button ta-btn" onClick={handleNavigateToTAs}>
            <FaUserFriends className="button-icon" />
            <span>View Teaching Assistants</span>
          </button>
          <button className="button excel-btn" onClick={exportLastExamSchedule} disabled={loading}>
            <FaFileExcel className="button-icon" />
            <span>Download Excel</span>
          </button>
        </div>

        <div className="button-group secondary-actions">
          <button className="button error-btn" onClick={handleShowErrors} disabled={loading}>
            <FaEye className="button-icon" />
            <span>{loading ? 'Loading Errors...' : 'View Scheduling Errors'}</span>
          </button>
          <button className="button recreate-btn" onClick={handleRecreateTable} disabled={loading}>
            <FaTrashAlt className="button-icon" />
            <span>{loading ? 'Processing...' : 'Delete Table'}</span>
          </button>
        </div>
      </div>

      {loading && <div className="loader">Loading...</div>}

      {showErrors && (
        <div className="modal-overlay">
          <div className="modal-content">
            <h3>Scheduling Errors</h3>
            <button className="close-btn" onClick={closeModal}>
              ×
            </button>
            <ul className="error-list">
              {!errors || errors.length === 0 ? (
                <li>No scheduling errors found.</li>
              ) : (
                errors.map((error, index) => (
                  <li key={index}>
                    {error.error_message} (Created At: {error.created_at})
                  </li>
                ))
              )}
            </ul>
          </div>
        </div>
      )}

      {showConfirmation && (
        <div className="modal-overlay">
          <div className="modal-content">
            <h3>Are you sure you want to recreate the table?</h3>
            <p>This action will clear all related data. Do you want to proceed?</p>
            <div className="modal-buttons">
              <button className="confirm-btn" onClick={confirmRecreateTable}>
                Yes
              </button>
              <button className="cancel-btn" onClick={cancelRecreateTable}>
                No
              </button>
            </div>
          </div>
        </div>
      )}

      <table className="ces-table">
        <thead>
          <tr>
            <th>Exam Day</th>
            <th>Exam Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Duration</th>
            <th>Subject Name</th>
            <th>Group Name</th>
            {/* <th>Subgroup</th>
            <th>Hall Name</th> */}
          </tr>
        </thead>
        <tbody>
          {examSchedules.length === 0 ? (
            <tr>
              <td colSpan="9">No exam schedules found.</td>
            </tr>
          ) : (
            examSchedules.map((schedule) => (
              <tr key={schedule.exam_id}>
                <td>{schedule.exam_day}</td>
                <td>{schedule.exam_date}</td>
                <td>{schedule.start_time}</td>
                <td>{schedule.end_time}</td>
                <td>{schedule.duration}</td>
                <td>{schedule.subject?.subject_name || 'N/A'}</td>
                <td>{schedule.group?.group_name || 'N/A'}</td>
                {/* <td>{schedule.subgroup?.subgroup_name || 'N/A'}</td>
                <td>{schedule.hall?.hall_name || 'N/A'}</td> */}
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  );
}

export default CreateExamSchedule;
