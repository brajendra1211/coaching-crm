<form method="POST" action="{{ route('lead.submit') }}">
    @csrf

    <input type="hidden" name="source" value="{{ $source ?? 'website' }}">

    @if(isset($course))
        <input type="hidden" name="course_id" value="{{ $course->id }}">
    @endif

    <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter student name" required>
    </div>

    <div class="form-group">
        <label>Phone Number *</label>
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Enter mobile number" required>
    </div>

    <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address">
    </div>

    <div class="form-group">
        <label>Class / Level</label>
        <input type="text" name="class_level" value="{{ old('class_level') }}" placeholder="Example: Class 11, Class 12, Dropper">
    </div>

    @if(isset($courses) && $courses->count())
        <div class="form-group">
            <label>Interested Course</label>
            <select name="course_id">
                <option value="">Select Course</option>
                @foreach($courses as $item)
                    <option value="{{ $item->id }}" {{ old('course_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->title }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="form-group">
        <label>Message</label>
        <textarea name="message" placeholder="Write your requirement">{{ old('message') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;">
        Submit Enquiry
    </button>
</form>