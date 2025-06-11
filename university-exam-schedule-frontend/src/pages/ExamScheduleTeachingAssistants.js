import React, { useState, useEffect } from 'react';
import { getTAAssignments, deleteTAAssignment } from '../services/api';
import * as XLSX from 'xlsx';
import './ExamScheduleTeachingAssistants.css';

const ExamScheduleTeachingAssistants = () => {
    const [assignments, setAssignments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedTA, setSelectedTA] = useState('');
    const [filteredAssignments, setFilteredAssignments] = useState([]);

    useEffect(() => {
        fetchAssignments();
    }, []);

    useEffect(() => {
        if (selectedTA) {
            setFilteredAssignments(assignments.filter(assignment => 
                assignment.ta_name === selectedTA
            ));
        } else {
            setFilteredAssignments(assignments);
        }
    }, [selectedTA, assignments]);

    const fetchAssignments = async () => {
        try {
            const response = await getTAAssignments();
            setAssignments(response.data);
            setFilteredAssignments(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to fetch TA assignments');
            console.error('Error fetching assignments:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (assignmentId) => {
        if (window.confirm('Are you sure you want to remove this assignment?')) {
            try {
                await deleteTAAssignment(assignmentId);
                await fetchAssignments();
            } catch (err) {
                setError('Failed to delete assignment');
                console.error('Error deleting assignment:', err);
            }
        }
    };

    const handleTAChange = (event) => {
        setSelectedTA(event.target.value);
    };

    const downloadExcel = () => {
        const dataToExport = selectedTA 
            ? filteredAssignments 
            : assignments;

        const worksheet = XLSX.utils.json_to_sheet(dataToExport.map(assignment => ({
            'TA Name': assignment.ta_name,
            'Exam Day': assignment.exam_day,
            'Exam Date': assignment.exam_date,
            'Start Time': assignment.start_time,
            'End Time': assignment.end_time,
            'Subject': assignment.subject_name,
            'Group': assignment.group_name,
            'Hall': assignment.hall_name,
            'Role': assignment.role,
            'Status': assignment.status
        })));

        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'TA Assignments');
        
        const fileName = selectedTA 
            ? `TA_Assignments_${selectedTA.replace(/\s+/g, '_')}.xlsx`
            : 'All_TA_Assignments.xlsx';
            
        XLSX.writeFile(workbook, fileName);
    };

    if (loading) return <div className="loading">Loading...</div>;
    if (error) return <div className="error-message">{error}</div>;

    const uniqueTAs = [...new Set(assignments.map(assignment => assignment.ta_name))];

    return (
        <div className="ta-assignments-container">
            <h2>Teaching Assistant Assignments</h2>
            
            <div className="controls-section">
                <div className="select-container">
                    <label htmlFor="ta-select">Select Teaching Assistant:</label>
                    <select 
                        id="ta-select" 
                        value={selectedTA} 
                        onChange={handleTAChange}
                        className="ta-select"
                    >
                        <option value="">All Teaching Assistants</option>
                        {uniqueTAs.map(ta => (
                            <option key={ta} value={ta}>{ta}</option>
                        ))}
                    </select>
                </div>
                
                <button 
                    onClick={downloadExcel}
                    className="download-btn"
                >
                    Download Excel
                </button>
            </div>

            <div className="assignments-table-container">
                <table className="assignments-table">
                    <thead>
                        <tr>
                            <th>Exam Day</th>
                            <th>Exam Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Subject</th>
                            <th>Group</th>
                            <th>Hall</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {filteredAssignments.map(assignment => (
                            <tr key={assignment.assignment_id}>
                                <td>{assignment.exam_day}</td>
                                <td>{assignment.exam_date}</td>
                                <td>{assignment.start_time}</td>
                                <td>{assignment.end_time}</td>
                                <td>{assignment.subject_name}</td>
                                <td>{assignment.group_name}</td>
                                <td>{assignment.hall_name}</td>
                                <td>{assignment.role}</td>
                                <td>{assignment.status}</td>
                                <td>
                                    <button
                                        onClick={() => handleDelete(assignment.assignment_id)}
                                        className="delete-btn"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default ExamScheduleTeachingAssistants;
