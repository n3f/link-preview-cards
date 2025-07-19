export default function Card({ url, title, description, image }) {
    if (!url) return null;
    return (
        <a
            href={url}
            target="_blank"
            rel="noopener noreferrer"
            className="card"
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
            {image && (
                <img
                    src={image}
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
                <div className="card-title" style={{ fontWeight: 'bold', fontSize: '1.1em', marginBottom: 8 }}>
                    {title || url}
                </div>
                {description && (
                    <div className="card-description" style={{ color: '#555', marginBottom: 12 }}>
                        {description}
                    </div>
                )}
                <div className="card-url" style={{ fontSize: '0.85em', color: '#888' }}>
                    {url.replace(/^https?:\/\//, '').split('/')[0]}
                </div>
            </div>
        </a>
    );
}
