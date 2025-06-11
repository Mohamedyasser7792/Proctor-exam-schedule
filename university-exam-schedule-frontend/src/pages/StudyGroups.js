import React, { useEffect, useState } from 'react';
import {
  getStudyGroups,
  createStudyGroup,
  updateStudyGroup,
  deleteStudyGroup,
  deleteAllStudyGroups,
} from '../services/api';
import './StudyGroups.css';

function StudyGroups() {
  const [studyGroups, setStudyGroups] = useState([]);
  const [formData, setFormData] = useState({
    group_name: '',
    number_of_groups: 1
  });
  const [editingId, setEditingId] = useState(null);

  useEffect(() => {
    fetchStudyGroups();
  }, []);

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
        response = await updateStudyGroup(editingId, formData);
        alert(response.data.message || 'Study Group updated successfully!');
      } else {
        response = await createStudyGroup(formData);
        alert(response.data.message || 'Study Group added successfully!');
      }

      await fetchStudyGroups();
      resetForm();
    } catch (error) {
      console.error('Error saving study group:', error);
      const errorMessage = error.response?.data?.message || 
                         error.response?.data?.error || 
                         'Error saving study group. Please try again.';
      alert(errorMessage);
    }
  };

  const handleEdit = (group) => {
    setFormData({
      group_name: group.group_name,
      number_of_groups: group.number_of_groups
    });
    setEditingId(group.group_id);
  };

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete this study group?'
    );
    if (confirmDelete) {
      try {
        await deleteStudyGroup(id);
        fetchStudyGroups();
        alert('Study group deleted successfully.');
      } catch (error) {
        console.error('Error deleting study group:', error);
        alert(error.response?.data?.error || 'Error deleting study group.');
      }
    }
  };

  const handleDeleteAll = async () => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete all study groups?'
    );
    if (confirmDelete) {
      try {
        await deleteAllStudyGroups();
        fetchStudyGroups();
        alert('All study groups have been deleted successfully.');
      } catch (error) {
        console.error('Error deleting all study groups:', error);
        alert(error.response?.data?.error || 'Error deleting all study groups.');
      }
    }
  };

  const resetForm = () => {
    setFormData({
      group_name: '',
      number_of_groups: 1
    });
    setEditingId(null);
  };

  return (
    <div className="study-groups-container">
      <h2>Study Groups</h2>
      <div className="study-groups-form">
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Group Name:</label>
            <input
              type="text"
              value={formData.group_name}
              onChange={(e) => setFormData({ ...formData, group_name: e.target.value })}
              required
              placeholder="Enter group name"
            />
          </div>

          <div className="form-group">
            <label>Number of Groups:</label>
            <input
              type="number"
              min="1"
              value={formData.number_of_groups}
              onChange={(e) => setFormData({ ...formData, number_of_groups: parseInt(e.target.value) })}
              required
            />
          </div>

          <div className="form-actions">
            <button type="submit" className="submit-btn">
              {editingId ? 'Update' : 'Add'} Study Group
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

      <div className="study-groups-table-container">
        <table className="study-groups-table">
          <thead>
            <tr>
              <th>Group Name</th>
              <th>Number of Groups</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {studyGroups.length > 0 ? (
              studyGroups.map((group) => (
                <tr key={group.group_id}>
                  <td>{group.group_name}</td>
                  <td>{group.number_of_groups}</td>
                  <td>
                    <button
                      className="edit-btn"
                      onClick={() => handleEdit(group)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(group.group_id)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="3">No study groups found.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default StudyGroups;
