import React, { useEffect, useState } from 'react';
import {
  getExamHalls,
  createExamHall,
  updateExamHall,
  deleteExamHall,
  deleteAllExamHalls,
} from '../services/api';
import './ExamHalls.css';

function ExamHalls() {
  const [examHalls, setExamHalls] = useState([]);
  const [formData, setFormData] = useState({
    hall_name: '',
    number_of_students: 1
  });
  const [editingId, setEditingId] = useState(null);

  useEffect(() => {
    fetchExamHalls();
  }, []);

  const fetchExamHalls = async () => {
    try {
      const response = await getExamHalls();
      setExamHalls(response.data);
    } catch (error) {
      console.error('Error fetching exam halls:', error);
      alert('Error fetching exam halls. Please try again.');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      let response;
      if (editingId) {
        response = await updateExamHall(editingId, formData);
        alert(response.data.message || 'Exam Hall updated successfully!');
      } else {
        response = await createExamHall(formData);
        alert(response.data.message || 'Exam Hall added successfully!');
      }

      await fetchExamHalls();
      resetForm();
    } catch (error) {
      console.error('Error saving exam hall:', error);
      const errorMessage = error.response?.data?.message || 
                         error.response?.data?.error || 
                         'Error saving exam hall. Please try again.';
      alert(errorMessage);
    }
  };

  const handleEdit = (hall) => {
    setFormData({
      hall_name: hall.hall_name,
      number_of_students: hall.number_of_students
    });
    setEditingId(hall.hall_id);
  };

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete this exam hall?'
    );
    if (confirmDelete) {
      try {
        await deleteExamHall(id);
        fetchExamHalls();
        alert('Exam hall deleted successfully.');
      } catch (error) {
        console.error('Error deleting exam hall:', error);
        alert(error.response?.data?.error || 'Error deleting exam hall.');
      }
    }
  };

  const handleDeleteAll = async () => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete all exam halls?'
    );
    if (confirmDelete) {
      try {
        await deleteAllExamHalls();
        fetchExamHalls();
        alert('All exam halls have been deleted successfully.');
      } catch (error) {
        console.error('Error deleting all exam halls:', error);
        alert(error.response?.data?.error || 'Error deleting all exam halls.');
      }
    }
  };

  const resetForm = () => {
    setFormData({
      hall_name: '',
      number_of_students: 1
    });
    setEditingId(null);
  };

  return (
    <div className="exam-halls-container">
      <h2>Exam Halls</h2>
      <div className="exam-halls-form">
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Hall Name:</label>
            <input
              type="text"
              value={formData.hall_name}
              onChange={(e) => setFormData({ ...formData, hall_name: e.target.value })}
              required
              placeholder="Enter hall name"
            />
          </div>

          <div className="form-group">
            <label>Number of Students:</label>
            <input
              type="number"
              min="1"
              value={formData.number_of_students}
              onChange={(e) => setFormData({ ...formData, number_of_students: parseInt(e.target.value) })}
              required
            />
          </div>

          <div className="form-actions">
            <button type="submit" className="submit-btn">
              {editingId ? 'Update' : 'Add'} Exam Hall
            </button>
            {editingId && (
              <button type="button" onClick={resetForm} className="cancel-btn">
                Cancel
              </button>
            )}
          </div>
        </form>

        <div className="table-actions">
          <button className="delete-all-button" onClick={handleDeleteAll}>
            Delete All
          </button>
        </div>
      </div>

      <div className="exam-halls-table-container">
        <table className="exam-halls-table">
          <thead>
            <tr>
              <th>Hall Name</th>
              <th>Number of Students</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {examHalls.length > 0 ? (
              examHalls.map((hall) => (
                <tr key={hall.hall_id}>
                  <td>{hall.hall_name}</td>
                  <td>{hall.number_of_students}</td>
                  <td>
                    <button
                      className="edit-btn"
                      onClick={() => handleEdit(hall)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(hall.hall_id)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="3">No exam halls found.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default ExamHalls;
