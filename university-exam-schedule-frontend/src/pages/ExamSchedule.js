import React, { useEffect, useState } from 'react';
import {
  getExamSchedules,
  createExamSchedule,
  updateExamSchedule,
  deleteExamSchedule,
  deleteAllExamSchedules,
  getStudySubjects,
  getStudyGroups
} from '../services/api';
import './ExamSchedule.css';

function ExamSchedule() {
  const [schedules, setSchedules] = useState([]);
  const [subjects, setSubjects] = useState([]);
  const [studyGroups, setStudyGroups] = useState([]);
  const [formData, setFormData] = useState({
    exam_day: '',
    exam_date: '',
    subject_id: '',
    group_id: '',
    start_time: '',
    end_time: '',
    duration: 60
  });
  const [editingId, setEditingId] = useState(null);

  useEffect(() => {
    fetchSchedules();
    fetchSubjects();
    fetchStudyGroups();
  }, []);

  const fetchSchedules = async () => {
    try {
      const response = await getExamSchedules();
      setSchedules(response.data);
    } catch (error) {
      console.error('Error fetching schedules:', error);
      alert('Error fetching schedules. Please try again.');
    }
  };

  const fetchSubjects = async () => {
    try {
      const response = await getStudySubjects();
      setSubjects(response.data);
    } catch (error) {
      console.error('Error fetching subjects:', error);
      alert('Error fetching subjects. Please try again.');
    }
  };

  const fetchStudyGroups = async () => {
    try {
      const response = await getStudyGroups();
      setStudyGroups(response.data);
    } catch (error) {
      console.error('Error fetching study groups:', error);
      alert('Error fetching study groups. Please try again.');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      let response;
      if (editingId) {
        response = await updateExamSchedule(editingId, formData);
        alert(response.data.message || 'Schedule updated successfully!');
      } else {
        response = await createExamSchedule(formData);
        alert(response.data.message || 'Schedule added successfully!');
      }

      await fetchSchedules();
      resetForm();
    } catch (error) {
      console.error('Error saving schedule:', error);
      const errorMessage = error.response?.data?.message || 
                         error.response?.data?.error || 
                         'Error saving schedule. Please try again.';
      alert(errorMessage);
    }
  };

  const handleEdit = (schedule) => {
    setFormData({
      exam_day: schedule.exam_day,
      exam_date: schedule.exam_date,
      subject_id: schedule.subject_id,
      group_id: schedule.group_id,
      start_time: schedule.start_time,
      end_time: schedule.end_time,
      duration: schedule.duration
    });
    setEditingId(schedule.exam_id);
  };

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete this schedule?'
    );
    if (confirmDelete) {
      try {
        await deleteExamSchedule(id);
        fetchSchedules();
        alert('Schedule deleted successfully.');
      } catch (error) {
        console.error('Error deleting schedule:', error);
        alert(error.response?.data?.error || 'Error deleting schedule.');
      }
    }
  };

  const handleDeleteAll = async () => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete all exam schedules? This action cannot be undone.'
    );
    if (confirmDelete) {
      try {
        await deleteAllExamSchedules();
        fetchSchedules();
        alert('All exam schedules have been deleted successfully.');
      } catch (error) {
        console.error('Error deleting all schedules:', error);
        alert(error.response?.data?.error || 'Error deleting all schedules.');
      }
    }
  };

  const resetForm = () => {
    setFormData({
      exam_day: '',
      exam_date: '',
      subject_id: '',
      group_id: '',
      start_time: '',
      end_time: '',
      duration: 60
    });
    setEditingId(null);
  };

  return (
    <div className="exam-schedule-container">
      <h2>Exam Schedules</h2>
      <div className="exam-schedule-form">
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Exam Day:</label>
            <input
              type="text"
              value={formData.exam_day}
              onChange={(e) => setFormData({ ...formData, exam_day: e.target.value })}
              required
              placeholder="Enter exam day"
            />
          </div>

          <div className="form-group">
            <label>Exam Date:</label>
            <input
              type="date"
              value={formData.exam_date}
              onChange={(e) => setFormData({ ...formData, exam_date: e.target.value })}
              required
            />
          </div>

          <div className="form-group">
            <label>Subject:</label>
            <select
              value={formData.subject_id}
              onChange={(e) => setFormData({ ...formData, subject_id: e.target.value })}
              required
            >
              <option value="">Select a subject</option>
              {subjects.map((subject) => (
                <option key={subject.subject_id} value={subject.subject_id}>
                  {subject.subject_name}
                </option>
              ))}
            </select>
          </div>

          <div className="form-group">
            <label>Study Group:</label>
            <select
              value={formData.group_id}
              onChange={(e) => setFormData({ ...formData, group_id: e.target.value })}
              required
            >
              <option value="">Select a study group</option>
              {studyGroups.map((group) => (
                <option key={group.group_id} value={group.group_id}>
                  {group.group_name}
                </option>
              ))}
            </select>
          </div>

          <div className="form-group">
            <label>Start Time:</label>
            <input
              type="time"
              value={formData.start_time}
              onChange={(e) => setFormData({ ...formData, start_time: e.target.value })}
              required
            />
          </div>

          <div className="form-group">
            <label>End Time:</label>
            <input
              type="time"
              value={formData.end_time}
              onChange={(e) => setFormData({ ...formData, end_time: e.target.value })}
              required
            />
          </div>

          <div className="form-group">
            <label>Duration (minutes):</label>
            <input
              type="number"
              value={formData.duration}
              onChange={(e) => setFormData({ ...formData, duration: parseInt(e.target.value) })}
              required
              min="1"
            />
          </div>

          <div className="form-actions">
            <button type="submit" className="submit-btn">
              {editingId ? 'Update' : 'Add'} Schedule
            </button>
            {editingId && (
              <button type="button" onClick={resetForm} className="cancel-btn">
                Cancel
              </button>
            )}
          </div>
        </form>

        <div className="table-actions">
          <button className="delete-all-btn" onClick={handleDeleteAll}>
            Delete All Schedules
          </button>
        </div>
      </div>

      <div className="exam-schedule-table-container">
        <table className="exam-schedule-table">
          <thead>
            <tr>
              <th>Exam Day</th>
              <th>Date</th>
              <th>Subject</th>
              <th>Study Group</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Duration</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {schedules.length > 0 ? (
              schedules.map((schedule) => (
                <tr key={schedule.exam_id}>
                  <td>{schedule.exam_day}</td>
                  <td>{new Date(schedule.exam_date).toLocaleDateString()}</td>
                  <td>{schedule.subject?.subject_name || 'N/A'}</td>
                  <td>{schedule.study_group?.group_name || 'N/A'}</td>
                  <td>{schedule.start_time}</td>
                  <td>{schedule.end_time}</td>
                  <td>{schedule.duration} min</td>
                  <td>
                    <button
                      className="edit-btn"
                      onClick={() => handleEdit(schedule)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(schedule.exam_id)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="8">No schedules found.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default ExamSchedule;
