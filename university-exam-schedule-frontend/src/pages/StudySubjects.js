import React, { useEffect, useState } from 'react';
import {
  getStudySubjects,
  createStudySubject,
  updateStudySubject,
  deleteStudySubject,
  deleteAllStudySubjects,
  getStudyGroups
} from '../services/api';
import './StudySubjects.css';

function StudySubjects() {
  const [subjects, setSubjects] = useState([]);
  const [studyGroups, setStudyGroups] = useState([]);
  const [formData, setFormData] = useState({
    subject_name: '',
    group_id: ''
  });
  const [editingId, setEditingId] = useState(null);

  useEffect(() => {
    fetchSubjects();
    fetchStudyGroups();
  }, []);

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
        response = await updateStudySubject(editingId, formData);
        alert(response.data.message || 'Subject updated successfully!');
      } else {
        response = await createStudySubject(formData);
        alert(response.data.message || 'Subject added successfully!');
      }

      await fetchSubjects();
      resetForm();
    } catch (error) {
      console.error('Error saving subject:', error);
      const errorMessage = error.response?.data?.message || 
                         error.response?.data?.error || 
                         'Error saving subject. Please try again.';
      alert(errorMessage);
    }
  };

  const handleEdit = (subject) => {
    setFormData({
      subject_name: subject.subject_name,
      group_id: subject.group_id
    });
    setEditingId(subject.subject_id);
  };

  const handleDelete = async (id) => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete this subject?'
    );
    if (confirmDelete) {
      try {
        await deleteStudySubject(id);
        fetchSubjects();
        alert('Subject deleted successfully.');
      } catch (error) {
        console.error('Error deleting subject:', error);
        alert(error.response?.data?.error || 'Error deleting subject.');
      }
    }
  };

  const handleDeleteAll = async () => {
    const confirmDelete = window.confirm(
      'Are you sure you want to delete all subjects?'
    );
    if (confirmDelete) {
      try {
        await deleteAllStudySubjects();
        fetchSubjects();
        alert('All subjects have been deleted successfully.');
      } catch (error) {
        console.error('Error deleting all subjects:', error);
        alert(error.response?.data?.error || 'Error deleting all subjects.');
      }
    }
  };

  const resetForm = () => {
    setFormData({
      subject_name: '',
      group_id: ''
    });
    setEditingId(null);
  };

  return (
    <div className="study-subjects-container">
      <h2>Study Subjects</h2>
      <div className="study-subjects-form">
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Subject Name:</label>
            <input
              type="text"
              value={formData.subject_name}
              onChange={(e) => setFormData({ ...formData, subject_name: e.target.value })}
              required
              placeholder="Enter subject name"
            />
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

          <div className="form-actions">
            <button type="submit" className="submit-btn">
              {editingId ? 'Update' : 'Add'} Subject
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

      <div className="study-subjects-table-container">
        <table className="study-subjects-table">
          <thead>
            <tr>
              <th>Subject Name</th>
              <th>Study Group</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {subjects.length > 0 ? (
              subjects.map((subject) => (
                <tr key={subject.subject_id}>
                  <td>{subject.subject_name}</td>
                  <td>{subject.study_group?.group_name || 'N/A'}</td>
                  <td>
                    <button
                      className="edit-btn"
                      onClick={() => handleEdit(subject)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(subject.subject_id)}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="3">No subjects found.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default StudySubjects;
