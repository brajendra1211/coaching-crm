<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Website Enquiry</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#334155;">
    <div style="max-width:680px;margin:0 auto;padding:24px;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;">
            <div style="background:#2563eb;color:#fff;padding:22px 24px;">
                <h1 style="margin:0;font-size:24px;">New Website Enquiry</h1>
                <p style="margin:8px 0 0;opacity:.9;">{{ $setting->institute_name ?: config('app.name') }}</p>
            </div>

            <div style="padding:24px;">
                <p style="margin:0 0 18px;line-height:1.6;">
                    A new enquiry has been submitted from the contact page.
                </p>

                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Name</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Phone</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->phone }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Email</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Class</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->class_level ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Course</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->course->title ?? 'General Enquiry' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;font-weight:700;">Message</td>
                        <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $lead->message ?: '-' }}</td>
                    </tr>
                </table>

                <p style="margin:18px 0 0;color:#64748b;font-size:13px;">
                    This enquiry is also saved in CRM Leads with source: website_contact.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
