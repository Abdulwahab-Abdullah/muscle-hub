<?php
return [
    // ==================== Authentication Messages ====================
    'user_registered' => 'تم تسجيل المستخدم بنجاح',
    'login_success' => 'تم تسجيل الدخول بنجاح',
    'logout_success' => 'تم تسجيل الخروج بنجاح',
    'invalid_credentials' => 'بيانات الدخول غير صحيحة',
    'unauthorized' => 'غير مصرح لك بالوصول',
    'user_not_authenticated' => 'المستخدم غير مسجل الدخول',

    // ==================== Profile Messages ====================
    'profile_updated' => 'تم تحديث الملف الشخصي بنجاح',
    'no_changes_detected' => 'لم يتم اكتشاف أي تغييرات',
    'password_changed' => 'تم تغيير كلمة المرور بنجاح',
    'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة',
    'avatar_uploaded' => 'تم رفع الصورة الشخصية بنجاح',
    'avatar_deleted' => 'تم حذف الصورة الشخصية بنجاح',
    'too_many_update_attempts' => 'عدد كبير جداً من محاولات التحديث. يرجى المحاولة مرة أخرى بعد :minutes دقيقة',

    // ==================== Validation Messages ====================
    'validation_required' => 'حقل :attribute مطلوب',
    'validation_email' => 'حقل :attribute يجب أن يكون بريد إلكتروني صالح',
    'validation_unique' => 'حقل :attribute مستخدم مسبقاً',
    'validation_min' => 'حقل :attribute يجب أن يكون على الأقل :min',
    'validation_max' => 'حقل :attribute يجب ألا يزيد عن :max',
    'validation_confirmed' => 'حقل تأكيد :attribute غير متطابق',

    // ==================== Chatbot Messages ====================
    'bot_failed' => 'عذراً، لم أستطع توليد رد مناسب',
    'server_error' => 'خطأ في الاتصال بالخادم',
    'unexpected_error' => 'حدث خطأ غير متوقع',
    'chat_cleared' => 'تم حذف المحادثة بنجاح',
    'api_error' => 'فشل الاتصال بـ API',
    'ai_service_unavailable' => 'خدمة الذكاء الاصطناعي غير متاحة مؤقتاً. يرجى المحاولة لاحقاً',
    'gemini_api_key_missing' => 'مفتاح Gemini API غير مُعد',

    // ==================== Nutrition Plan Messages ====================
    'plan_saved' => 'تم حفظ الخطة بنجاح',
    'plan_updated' => 'تم تحديث الخطة بنجاح',
    'plan_deleted' => 'تم حذف خطة التغذية بنجاح',
    'plan_not_found' => 'لم يتم العثور على الخطة',
    'meals_deleted' => 'تم حذف وجبات اليوم بنجاح',
    'failed_to_save_plan' => 'فشل في حفظ الخطة',
    'failed_to_update_plan' => 'فشل في تحديث الخطة',

    // ==================== Progress Messages ====================
    'progress_recorded' => 'تم تسجيل التقدم بنجاح',
    'progress_created' => 'تم إنشاء سجل التقدم بنجاح',
    'progress_updated' => 'تم تحديث سجل التقدم بنجاح',
    'progress_deleted' => 'تم حذف سجل التقدم بنجاح',
    'progress_not_found' => 'سجل التقدم غير موجود',

    // ==================== Tips Messages ====================
    'tip_not_found' => 'النصيحة غير موجودة',
    'tip_created' => 'تم إنشاء النصيحة بنجاح',
    'tip_updated' => 'تم تحديث النصيحة بنجاح',
    'tip_deleted' => 'تم حذف النصيحة بنجاح',

    // ==================== User Goal Messages ====================
    'user_has_goal' => 'المستخدم لديه هدف محدد مسبقاً',
    'goal_saved' => 'تم حفظ الهدف بنجاح',
    'goal_created' => 'تم إنشاء هدف المستخدم بنجاح',
    'goal_updated' => 'تم تحديث الهدف بنجاح',
    'no_goal_found' => 'لم يتم العثور على هدف. يرجى تحديد هدفك أولاً',

    // ==================== Workout Plan Messages ====================
    'workout_plan_created' => 'تم إنشاء خطة التمرين بنجاح',
    'workout_plan_updated' => 'تم تحديث خطة التمرين بنجاح',
    'workout_plan_deleted' => 'تم حذف خطة التمرين بنجاح',
    'workout_plan_not_found' => 'خطة التمرين غير موجودة',
    'duplicate_workout_plan_name' => 'اسم خطة التمرين موجود مسبقاً',
    'duplicate_workout_same_day' => 'لا يمكن تكرار نفس التمرين في نفس اليوم',

    // ==================== Achievement Messages ====================
    'achievement_unlocked' => 'تم فتح الإنجاز!',
    'achievement_not_found' => 'الإنجاز غير موجود',

    // ==================== Contact Messages ====================
    'message_sent' => 'تم إرسال الرسالة بنجاح',
    'failed_to_send_message' => 'فشل في إرسال الرسالة',

    // ==================== General Messages ====================
    'success' => 'نجح',
    'failed' => 'فشل',
    'not_found' => 'غير موجود',
    'forbidden' => 'ممنوع',
    'deleted_successfully' => 'تم الحذف بنجاح',
    'created_successfully' => 'تم الإنشاء بنجاح',
    'updated_successfully' => 'تم التحديث بنجاح',
];
