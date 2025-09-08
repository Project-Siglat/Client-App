import React, { useState } from 'react';

interface VerificationType {
  Id: number;
  Name: string;
  Description: string;
  IsActive: boolean;
}

interface AccountVerificationFormProps {
  verificationTypes: VerificationType[];
  onSubmit: (formData: FormData) => Promise<void>;
  onCancel: () => void;
}

const AccountVerificationForm: React.FC<AccountVerificationFormProps> = ({
  verificationTypes,
  onSubmit,
  onCancel
}) => {
  const [selectedType, setSelectedType] = useState<number | null>(null);
  const [documentNumber, setDocumentNumber] = useState('');
  const [documentName, setDocumentName] = useState('');
  const [documentImage, setDocumentImage] = useState<File | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [previewUrl, setPreviewUrl] = useState<string | null>(null);

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      // Validate file type
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!allowedTypes.includes(file.type)) {
        alert('Please select a valid image file (JPEG, PNG, or GIF)');
        return;
      }

      // Validate file size (5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
      }

      setDocumentImage(file);
      
      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        setPreviewUrl(e.target?.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!selectedType) {
      alert('Please select a verification type');
      return;
    }

    if (!documentImage) {
      alert('Please upload a document image');
      return;
    }

    setIsSubmitting(true);

    try {
      const formData = new FormData();
      formData.append('verificationTypeId', selectedType.toString());
      formData.append('documentNumber', documentNumber);
      formData.append('documentName', documentName);
      formData.append('documentImage', documentImage);

      await onSubmit(formData);
    } catch (error) {
      console.error('Error submitting verification:', error);
    } finally {
      setIsSubmitting(false);
    }
  };

  const selectedTypeData = verificationTypes.find(t => t.Id === selectedType);

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {/* Verification Type Selection */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-3">
          Select Document Type *
        </label>
        <div className="space-y-3">
          {verificationTypes.length === 0 ? (
            <div className="text-center py-4 text-gray-500">
              Loading document types...
            </div>
          ) : (
            verificationTypes.map((type) => (
            <div
              key={type.Id}
              className={`border-2 rounded-lg p-4 cursor-pointer transition-all ${
                selectedType === type.Id
                  ? 'border-red-500 bg-red-50'
                  : 'border-gray-200 hover:border-gray-300'
              }`}
              onClick={() => setSelectedType(type.Id)}
            >
              <div className="flex items-center space-x-3">
                <input
                  type="radio"
                  name="verificationType"
                  value={type.Id}
                  checked={selectedType === type.Id}
                  onChange={() => setSelectedType(type.Id)}
                  className="text-red-600 focus:ring-red-500"
                />
                <div>
                  <h4 className="font-medium text-gray-900">{type.Name}</h4>
                  <p className="text-sm text-gray-500">{type.Description}</p>
                </div>
              </div>
            </div>
            ))
          )}
        </div>
      </div>

      {/* Document Details */}
      {selectedType && (
        <>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Document Number (Optional)
            </label>
            <input
              type="text"
              value={documentNumber}
              onChange={(e) => setDocumentNumber(e.target.value)}
              placeholder={`Enter your ${selectedTypeData?.Name} number`}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            />
            <p className="mt-1 text-xs text-gray-500">
              This helps us verify your document faster
            </p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Document Name (Optional)
            </label>
            <input
              type="text"
              value={documentName}
              onChange={(e) => setDocumentName(e.target.value)}
              placeholder="e.g., John Doe's Passport"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            />
          </div>

          {/* Image Upload */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Upload Document Image *
            </label>
            <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
              {previewUrl ? (
                <div className="space-y-4">
                  <img
                    src={previewUrl}
                    alt="Document preview"
                    className="mx-auto max-w-full max-h-48 rounded-md shadow-sm"
                  />
                  <div>
                    <p className="text-sm text-gray-600 mb-2">
                      {documentImage?.name}
                    </p>
                    <button
                      type="button"
                      onClick={() => {
                        setDocumentImage(null);
                        setPreviewUrl(null);
                      }}
                      className="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                      Remove Image
                    </button>
                  </div>
                </div>
              ) : (
                <div className="space-y-3">
                  <svg className="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <div>
                    <label htmlFor="document-upload" className="cursor-pointer">
                      <span className="text-red-600 hover:text-red-700 font-medium">Upload a file</span>
                      <span className="text-gray-500"> or drag and drop</span>
                    </label>
                    <input
                      id="document-upload"
                      type="file"
                      accept="image/*"
                      onChange={handleImageChange}
                      className="sr-only"
                    />
                  </div>
                  <p className="text-xs text-gray-500">
                    PNG, JPG, GIF up to 5MB
                  </p>
                </div>
              )}
            </div>
            <div className="mt-2 bg-blue-50 border border-blue-200 rounded-md p-3">
              <div className="flex items-start space-x-2">
                <svg className="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div className="text-sm text-blue-700">
                  <p className="font-medium mb-1">Tips for better verification:</p>
                  <ul className="text-xs space-y-1">
                    <li>• Ensure the document is clearly visible and not blurry</li>
                    <li>• Make sure all text and details are readable</li>
                    <li>• Avoid shadows or reflections on the document</li>
                    <li>• Take the photo in good lighting</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </>
      )}

      {/* Action Buttons */}
      <div className="flex space-x-3 pt-4 border-t">
        <button
          type="button"
          onClick={onCancel}
          disabled={isSubmitting}
          className="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 font-medium transition-colors disabled:opacity-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={!selectedType || !documentImage || isSubmitting}
          className="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center"
        >
          {isSubmitting ? (
            <>
              <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
              Submitting...
            </>
          ) : (
            'Submit for Review'
          )}
        </button>
      </div>
    </form>
  );
};

export default AccountVerificationForm;