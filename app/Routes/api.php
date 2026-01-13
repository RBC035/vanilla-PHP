<?php

return [
    'GET' => [
        // Auth / Users 
        '/auth' => ['AuthController', 'index'],
        '/auth/count' => ['AuthController', 'count'],
        '/auth/byUsername' => ['AuthController', 'getByUsername'],

        // Comments
        '/comments' => ['CommentController', 'index'],
        '/comments/count' => ['CommentController', 'count'],
        '/comments/by-coordinator-email' => ['CommentController', 'getByCoordinator'],
        '/comments/by-progress-id' => ['CommentController', 'getByProgressId'],
        '/comments/by-commented-date' => ['CommentController', 'getByDate'],

        // Coordinators
        '/coordinators' => ['CoordinatorController', 'index'],
        '/coordinators/count' => ['CoordinatorController', 'count'],
        '/coordinators/email' => ['CoordinatorController', 'getByEmail'],
        '/coordinators/{id}' => ['CoordinatorController', 'show'],

        
        // Progress
        '/progress' => ['ProgressController', 'index'],
        '/progress/count' => ['ProgressController', 'getCount'],
        '/progress/{id}' => ['ProgressController', 'show'],
        '/progress/student/{id}' => ['ProgressController', 'getByStudent'],
        '/progress/study/{id}' => ['ProgressController', 'getByStudy'],
        '/progress/latest/5/{id}' => ['ProgressController', 'getLatestFive'],
        '/progress/progressDate' => ['ProgressController', 'getByDate'],
        '/progress/studyComplition' => ['ProgressController', 'getByCompletion'],
        '/progress/submitThesis' => ['ProgressController', 'getByThesis'],
        '/progress/meetSupervisor' => ['ProgressController', 'getBySupervisorMeeting'],
        '/progress/supervisorResponse' => ['ProgressController', 'getByResponse'],
        '/progress/count/student/{id}' => ['ProgressController', 'getCountByStudent'],
        '/progress/file/{filename:.+}' => ['ProgressController', 'download'],

        // Students
        '/students' => ['StudentController', 'index'],
        '/students/count' => ['StudentController', 'count'],
        '/students/{id}' => ['StudentController', 'show'],
        '/students/search/by-name' => ['StudentController', 'searchByName'],
        '/students/search/by-email' => ['StudentController', 'searchByEmail'],
        '/students/search/by-gender' => ['StudentController', 'searchByGender'],
        '/students/search/by-phone' => ['StudentController', 'searchByPhone'],
        '/students/{id}/profileImage' => ['StudentController', 'getProfileImage'],

        // Studies

        '/studies' => ['StudyController', 'index'],
        '/studies/count' => ['StudyController', 'getCount'],
        '/studies/{id}' => ['StudyController', 'show'],
        '/studies/search/by-level/{level}' => ['StudyController', 'searchByLevel'],
        '/studies/search/by-university/{universityName}' => ['StudyController', 'searchByUniversity'],
        '/studies/search/by-country/{country}' => ['StudyController', 'searchByCountry'],
        '/studies/search/by-student/{studentId}' => ['StudyController', 'searchByStudent'],
        '/studies/count/by-student/{studentId}' => ['StudyController', 'countByStudent'], 

    ],

    'POST' => [
        '/auth/login' => ['AuthController', 'login'],
        '/auth/signup' => ['AuthController', 'register'],
        '/comments' => ['CommentController', 'store'],
        '/coordinators' => ['CoordinatorController', 'create'],
        '/otp/request-otp' => ['OtpController', 'request'],
        '/otp/verify-otp' => ['OtpController', 'verify'],
        '/progress' => ['ProgressController', 'store'],
        '/progress/{id}/upload' => ['ProgressController', 'uploadFile'],
        '/students' => ['StudentController', 'create'],
        '/students/{id}/uploadProfileImage' => ['StudentController', 'uploadImage'],
        '/studies' => ['StudyController', 'store'],
    ],

    'PUT' => [
        '/auth/{id}' => ['AuthController', 'changePassword'],
        '/coordinators/{id}' => ['CoordinatorController', 'update'],
        '/progress/{id}' => ['ProgressController', 'update'],
        '/progress/{id}/status' => ['ProgressController', 'updateStatus'],
        '/students/{id}' => ['StudentController', 'update'],
        '/studies/{id}' => ['StudyController', 'update'],
    ],

    'PATCH' => [
        '/coordinators/{id}/status' => ['CoordinatorController', 'updateStatus'],
        '/students/{id}/status' => ['StudentController', 'updateStatus'],
    ],

    'DELETE' => [
        '/coordinators/{id}' => ['CoordinatorController', 'delete'],
        '/progress/{id}' => ['ProgressController', 'delete'],
        '/students/{id}' => ['StudentController', 'delete'],
        '/studies/{id}' => ['StudyController', 'delete'],
    ]
];
