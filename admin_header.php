<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: sign.html'); exit; }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Wejhatna Admin Panel</title>
    <style>
        body { font-family:
                system-ui, sans-serif;
            background-color: #f4f7f6; margin: 0; }
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 240px; background-color: rgba(92, 115, 69, 0.86);;
            color: #ecf0f1;
            flex-shrink: 0; } .sidebar-header
                                      { text-align: center;
                                          padding: 20px;
                                          border-bottom: 1px solid rgba(92, 115, 69, 0.86); }
        .sidebar-header h2 { margin: 0; color: #fff; }
        .admin-sidebar nav ul { list-style: none; padding: 0; margin: 20px 0; }
        .admin-sidebar nav a { display: block; padding: 15px 25px;
            color: #ecf0f1;
            text-decoration: none; transition: all 0.3s; }
        .admin-sidebar nav a:hover { background-color: rgba(92, 115, 69, 0.86); }
        .admin-sidebar nav a.active { background-color: #7a7575;
            font-weight: bold; } .admin-main-content
                                         { flex-grow: 1; padding: 30px; }
        .content-header { display: flex; justify-content: space-between;
            align-items: center; border-bottom: 2px solid #dee2e6;
            padding-bottom: 15px; margin-bottom: 25px; }
        .content-header h1 { margin: 0; } .logout-link {
                                              background-color: #e74c3c;
                                              color: white; padding: 8px 15px;
                                              border-radius: 5px; text-decoration:
                    none; font-weight: bold; } .content-table
                                                                                         { width: 100%; border-collapse: collapse; box-shadow: 0 2px 10px rgba(0,0,0,0.07); background: #fff; border-radius: 8px; overflow: hidden; }
        .content-table th, .content-table td { padding: 15px; text-align:
                left; border-bottom: 1px solid #f0f0f0; }
        .content-table thead {
            background-color: rgba(92, 115, 69, 0.86);
            color: #fff; }  .content-table tbody tr:hover
                                    { background-color: #f9f9f9; }
        .btn { padding: 10px 18px; text-decoration: none;
            border-radius: 5px;
            font-weight: bold; border: none; cursor: pointer; }
        .content-form { background: #fff; padding: 30px; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px;
            border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .form-group textarea { min-height: 120px; }
        .btn-green  { background-color: rgba(92, 115, 69, 0.86); color: white; }
        .btn-blue { background-color: #3498db; color: white; }
        .btn-red { background-color: #e74c3c; color: white; }
        .btn-grey { background-color: #95a5a6; color: white; }
    </style>
</head><body><div class="admin-layout">