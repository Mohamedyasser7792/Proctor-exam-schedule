import axios from 'axios';
import { saveAs } from 'file-saver';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

export const exportTeachingAssistantDetails = async (ta_id) => {
  try {
    const response = await api.get(`/export-teaching-assistant/${ta_id}`, {
      responseType: 'blob', // Ensures the response is treated as a binary file
    });

    // Create a link to trigger the download
    const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `teaching_assistant_${ta_id}_details.xlsx`); // File name
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Error downloading Excel file:', error);
    alert('Failed to download the file. Please try again.');
  }
};



export const exportLastExamSchedule = async () => {
  try {
    const response = await api.get('/export-last-exam-schedule', {
      responseType: 'blob', // Ensure the response is treated as a binary blob
    });

    // Handle the blob directly for downloading
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'last_exam_schedule.xlsx'); // File name
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Error downloading the Excel file:', error);
    alert('Failed to download the file. Please try again.');
  }
};






// Login API
export const loginUser = (credentials) => api.post('/login', credentials);

// Teaching Assistants APIs
export const getTeachingAssistants = () => api.get('/teaching-assistants');
export const addTeachingAssistant = (data) => api.post('/teaching-assistants', data);
export const updateTeachingAssistant = async (id, data) => {
  const response = await api.put(`/teaching-assistants/${id}`, {
    name: data.name,
    status: data.status,
    role: data.role,
    join_date: data.joinDate,
    day_offs: data.dayOffs
  });
  return response.data;
};
export const deleteTeachingAssistant = (id) => api.delete(`/teaching-assistants/${id}`);
export const deleteAllTeachingAssistants = () => api.delete('/teaching-assistants/delete-all');


// Study Groups APIs
export const getStudyGroups = () => api.get('/study-groups');
export const createStudyGroup = (data) => api.post('/study-groups', data);
export const updateStudyGroup = (id, data) => api.put(`/study-groups/${id}`, data);
export const deleteStudyGroup = (id) => api.delete(`/study-groups/${id}`);
export const deleteAllStudyGroups = () => api.delete('/study-groups');

// Exam Halls APIs
export const getExamHalls = () => api.get('/exam-halls');
export const createExamHall = (data) => api.post('/exam-halls', data);
export const updateExamHall = (id, data) => api.put(`/exam-halls/${id}`, data);
export const deleteExamHall = (id) => api.delete(`/exam-halls/${id}`);
export const deleteAllExamHalls = () => api.delete('/exam-halls');

// Study Subjects APIs
export const getStudySubjects = () => api.get('/study-subjects');
export const createStudySubject = (data) => api.post('/study-subjects', data);
export const updateStudySubject = (id, data) => api.put(`/study-subjects/${id}`, data);
export const deleteStudySubject = (id) => api.delete(`/study-subjects/${id}`);
export const deleteAllStudySubjects = () => api.delete('/study-subjects');

// Exam Schedules APIs
export const getExamSchedules = () => api.get('/exam-schedules');
export const createExamSchedule = (data) => api.post('/exam-schedules', data);
export const updateExamSchedule = (id, data) => api.put(`/exam-schedules/${id}`, data);
export const deleteExamSchedule = (id) => api.delete(`/exam-schedules/${id}`);
export const deleteAllExamSchedules = () => api.delete('/exam-schedules');
export const getExamSchedule = (id) => api.get(`/exam-schedules/${id}`);

// Generate Exam Schedule API with optional payload
export const generateExamSchedule = (payload = {}) => api.post('/generate-exam-schedule', payload);

// New API to get the latest exam schedules
export const getLatestExamSchedules = () => api.get('/latest-exam-schedules');

// API to get scheduling errors
export const getSchedulingErrors = () => api.get('/scheduling-errors');

// API to clear specific data
export const clearData = async () => {
  try {
    const response = await api.delete('/clear-data');
    return response;
  } catch (error) {
    console.error('Error in clearData:', error.response?.data || error.message);
    throw error;
  }
};

// API to get all data from the exam_schedule_teaching_assistants table
export const getExamScheduleTeachingAssistants = () => api.get('/exam-schedule-teaching-assistants');

export const createTeachingAssistant = async (data) => {
  console.log('Sending data to server:', data); // Add logging
  const response = await api.post('/teaching-assistants', {
    name: data.name,
    status: data.status,
    role: data.role,
    join_date: data.joinDate, // Changed to match backend expectation
    day_offs: data.day_offs || []
  });
  return response.data;
};

// Latest Exam Schedule APIs
export const getLatestExamSchedule = () => api.get('/latest-exam-schedules');
export const createLatestExamSchedule = (data) => api.post('/latest-exam-schedules', data);
export const deleteLatestExamSchedule = (id) => api.delete(`/latest-exam-schedules/${id}`);

// Scheduling Errors APIs
export const createSchedulingError = (data) => api.post('/scheduling-errors', data);
export const deleteSchedulingError = (id) => api.delete(`/scheduling-errors/${id}`);

// TA Assignment APIs
export const getTAAssignments = () => api.get('/exam-schedule-teaching-assistants');
export const createTAAssignment = (data) => api.post('/exam-schedule-teaching-assistants', data);
export const deleteTAAssignment = (id) => api.delete(`/exam-schedule-teaching-assistants/${id}`);

export default api;
