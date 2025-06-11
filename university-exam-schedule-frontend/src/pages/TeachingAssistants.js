import React, { useEffect, useState } from "react";
import DatePicker from 'react-multi-date-picker';
import {
  getTeachingAssistants,
  deleteTeachingAssistant,
  deleteAllTeachingAssistants,
  createTeachingAssistant,
  updateTeachingAssistant,
} from "../services/api";
import "./TeachingAssistants.css";

function TeachingAssistants() {
  const [teachingAssistants, setTeachingAssistants] = useState([]);
  const [formData, setFormData] = useState({
    name: '',
    status: 'Basic',
    role: 'Teaching Assistant',
    joinDate: new Date().toISOString().split('T')[0]
  });
  const [editingId, setEditingId] = useState(null);
  const [dayOff, setDayOff] = useState([]);

  useEffect(() => {
    fetchTeachingAssistants();
  }, []);

  const fetchTeachingAssistants = async () => {
    try {
      const response = await getTeachingAssistants();
      setTeachingAssistants(response.data);
    } catch (error) {
      console.error("Error fetching teaching assistants:", error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      // Validate day offs
      if (dayOff.length === 0) {
        alert('Please select at least one day off');
        return;
      }

      // Format day offs properly
      const formattedDayOffs = dayOff.map(date => {
        if (date instanceof Date) {
          return date.toISOString().split('T')[0];
        } else if (typeof date === 'string') {
          return date;
        } else if (date && date.toDate) {
          return date.toDate().toISOString().split('T')[0];
        }
        return null;
      }).filter(date => date !== null);

      const dataToSubmit = {
        name: formData.name,
        status: formData.status,
        role: formData.role,
        joinDate: formData.joinDate,
        day_offs: formattedDayOffs
      };

      let response;
      if (editingId) {
        response = await updateTeachingAssistant(editingId, dataToSubmit);
        alert(response.message || 'Teaching Assistant updated successfully!');
      } else {
        response = await createTeachingAssistant(dataToSubmit);
        alert(response.message || 'Teaching Assistant added successfully!');
      }

      await fetchTeachingAssistants();
      resetForm();
    } catch (error) {
      console.error('Error saving teaching assistant:', error);
      console.error('Error details:', error.response?.data);
      const errorMessage = error.response?.data?.message || 
                         error.response?.data?.error || 
                         'Error saving teaching assistant. Please try again.';
      alert(errorMessage);
    }
  };

  const handleEdit = (ta) => {
    setFormData({
      name: ta.name,
      status: ta.status,
      role: ta.role,
      joinDate: new Date(ta.join_date).toISOString().split('T')[0]
    });
    setDayOff(ta.day_offs ? ta.day_offs.map(off => new Date(off.day_off)) : []);
    setEditingId(ta.ta_id);
  };

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      "Are you sure you want to delete this teaching assistant?"
    );
    if (confirmDelete) {
      try {
        await deleteTeachingAssistant(id);
        fetchTeachingAssistants();
      } catch (error) {
        console.error("Error deleting teaching assistant:", error);
        alert(error.response?.data?.error || "Error deleting teaching assistant.");
      }
    }
  };

  const handleDeleteAll = async () => {
    const confirmDelete = window.confirm(
      "Are you sure you want to delete all teaching assistants?"
    );
    if (confirmDelete) {
      try {
        await deleteAllTeachingAssistants();
        fetchTeachingAssistants();
        alert("All teaching assistants have been deleted successfully.");
      } catch (error) {
        console.error("Error deleting all teaching assistants:", error);
        alert(error.response?.data?.error || "Error deleting all teaching assistants.");
      }
    }
  };

  const resetForm = () => {
    setFormData({
      name: '',
      status: 'Basic',
      role: 'Teaching Assistant',
      joinDate: new Date().toISOString().split('T')[0]
    });
    setDayOff([]);
    setEditingId(null);
  };

  return (
    <div className="ta-page-container">
      <h2>Teaching Assistants</h2>
      <div className="ta-form">
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Name:</label>
            <input
              type="text"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              required
            />
          </div>

          <div className="form-group">
            <label>Status:</label>
            <select
              value={formData.status}
              onChange={(e) => setFormData({ ...formData, status: e.target.value })}
            >
              <option value="Basic">Basic</option>
              <option value="Reserve">Reserve</option>
            </select>
          </div>

          <div className="form-group">
            <label>Role:</label>
            <select
              value={formData.role}
              onChange={(e) => setFormData({ ...formData, role: e.target.value })}
            >
              <option value="Teaching Assistant">Teaching Assistant</option>
              <option value="Doctor">Doctor</option>
            </select>
          </div>

          <div className="form-group">
            <label>Join Date:</label>
            <input
              type="date"
              value={formData.joinDate}
              onChange={(e) => setFormData({ ...formData, joinDate: e.target.value })}
              required
            />
          </div>

          <div className="form-group">
            <label>Day Offs:</label>
            <DatePicker
              multiple
              value={dayOff}
              onChange={setDayOff}
              format="YYYY-MM-DD"
              placeholder="Select Day Offs"
            />
          </div>

          <div className="form-actions">
            <button type="submit" className="submit-btn">
              {editingId ? 'Update' : 'Add'} Teaching Assistant
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

      <div className="ta-table-container">
        <table className="ta-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Day Off</th>
              <th>Status</th>
              <th>Role</th>
              <th>Join Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {teachingAssistants.length > 0 ? (
              teachingAssistants.map((ta) => (
                <tr key={ta.ta_id}>
                  <td>{ta.name}</td>
                  <td>
                    {ta.day_offs && ta.day_offs.length > 0
                      ? ta.day_offs.map((dayOff, index) => (
                          <span key={index}>
                            {dayOff.day_off}
                            {index < ta.day_offs.length - 1 && ", "}
                          </span>
                        ))
                      : "No days off"}
                  </td>
                  <td>{ta.status}</td>
                  <td>{ta.role}</td>
                  <td>{new Date(ta.join_date).toLocaleDateString()}</td>
                  <td>
                    <button
                      className="edit-btn"
                      onClick={() => handleEdit(ta)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(ta.ta_id)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="6">No teaching assistants found.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default TeachingAssistants;
