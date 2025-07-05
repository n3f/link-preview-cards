export default function OgCard({ url, ogTitle, ogDescription, ogImage }) {
    if (!url) return null;
    return (
        <a
            href={url}
            target="_blank"
            rel="noopener noreferrer"
            className="og-card"
            style={{
                display: 'block',
                border: '1px solid #ccc',
                borderRadius: 8,
                padding: 0,
                textDecoration: 'none',
                color: 'inherit',
                overflow: 'hidden',
                boxShadow: '0 2px 8px rgba(0,0,0,0.04)'
            }}
        >
            {ogImage && (
                <img
                    src={ogImage}
                    alt=""
                    style={{
                        width: '100%',
                        height: 300,
                        objectFit: 'cover',
                        display: 'block',
                        borderTopLeftRadius: 8,
                        borderTopRightRadius: 8
                    }}
                />
            )}
            <div style={{ padding: 16 }}>
                <div className="og-card-title" style={{ fontWeight: 'bold', fontSize: '1.1em', marginBottom: 8 }}>
                    {ogTitle || url}
                </div>
                {ogDescription && (
                    <div className="og-card-description" style={{ color: '#555', marginBottom: 12 }}>
                        {ogDescription}
                    </div>
                )}
                <div style={{ fontSize: '0.85em', color: '#888' }}>
                    {url.replace(/^https?:\/\//, '').split('/')[0]}
                </div>
            </div>
        </a>
    );
}
